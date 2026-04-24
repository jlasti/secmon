<?php

namespace app\commands;

use app\models\MispAttributes;
use app\models\SecurityEvents;
use Yii;
use yii\console\Controller;
use yii\console\Exception;
use ZMQ;
use ZMQContext;

require '/var/www/html/secmon/vendor/autoload.php';

class MispController extends Controller
{
    /** Main loop receiving and enriching events with MISP data. */
    public function actionIndex()
    {
        $aggregator_config_file = $this->openNonBlockingStream("/var/www/html/secmon/config/aggregator_config.ini");
        $save_to_db = 0;
        $module_loaded = false;
        $next_module = "correlator";

        if ($aggregator_config_file) {
            while (($line = fgets($aggregator_config_file)) !== false) {
                if ($module_loaded == true) {
                    $parts = explode(":", $line);
                    $next_module = strtolower(trim($parts[0]));
                    $module_loaded = false;
                }

                if (strpos($line, "Misp:") !== false) {
                    $parts = explode(":", $line);
                    $port = trim($parts[1]);
                    $module_loaded = true;
                }
            }
        } else {
            throw new Exception('Could not open a config file');
        }

        $middleware_config_file = $this->openNonBlockingStream("/var/www/html/secmon/config/secmon_config.ini");
        if ($middleware_config_file) {
            while (($line = fgets($middleware_config_file)) !== false) {
                if (strpos($line, "host =") !== false) {
                    $parts = explode("=", $line);
                    $host = trim($parts[1]);
                }
                if (strpos($line, "database =") !== false) {
                    $parts = explode("=", $line);
                    $database = trim($parts[1]);
                }
                if (strpos($line, "user =") !== false) {
                    $parts = explode("=", $line);
                    $user = trim($parts[1]);
                }
                if (strpos($line, "password =") !== false) {
                    $parts = explode("=", $line);
                    $password = trim($parts[1]);
                }
            }
        } else {
            throw new Exception('Not all arguments were specified');
        }

        fclose($aggregator_config_file);
        fclose($middleware_config_file);

        $aggregator_config_file_path = escapeshellarg("/var/www/html/secmon/config/aggregator_config.ini");
        $last_line = `tail -n 1 $aggregator_config_file_path`;

        if (strpos($last_line, "Misp:") !== false) {
            $save_to_db = 1;
        }

        if (!is_numeric($port)) {
            throw new Exception('One of ports is not a numeric value');
        }

        $zmq = new ZMQContext();
        $recSocket = $zmq->getSocket(ZMQ::SOCKET_PULL);
        $recSocket->bind("tcp://*:" . $port);

        $sendSocket = $zmq->getSocket(ZMQ::SOCKET_PUSH);
        $sendSocket->connect("tcp://secmon_" . $next_module . ":" . $port);

        date_default_timezone_set("Europe/Bratislava");
        echo "[" . date("Y-m-d H:i:s") . "] Worker MISP started!" . PHP_EOL;

        while (true) {
            $msg = $recSocket->recv(ZMQ::MODE_NOBLOCK);
            if (empty($msg)) {
                usleep(100000);
                continue;
            }

            $srcIp = $this->extractField($msg, "src=");
            $dstIp = $this->extractField($msg, "dst=");

            $mispMatch = false;
            $hitInfo = "";
            
            $ipsToCheck = [];
            if ($srcIp) {
                $normalizedSrc = $this->normalizeIp($srcIp);
                if ($normalizedSrc) {
                    $ipsToCheck['src'] = $normalizedSrc;
                }
            }
            if ($dstIp) {
                $normalizedDst = $this->normalizeIp($dstIp);
                if ($normalizedDst && !in_array($normalizedDst, $ipsToCheck)) {
                    $ipsToCheck['dst'] = $normalizedDst;
                }
            }
            
            if (!empty($ipsToCheck)) {
                $allIps = array_values($ipsToCheck);
                
                $hits = MispAttributes::find()
                    ->select([
                        'misp_attributes.attribute_id',
                        'misp_attributes.value',
                        'misp_attributes.type',
                        'misp_attributes.event_id',
                        'misp_events.threat_level',
                        'misp_events.tags'
                    ])
                    ->joinWith('mispEvent')
                    ->where(['misp_attributes.value' => $allIps])
                    ->andWhere(['in', 'misp_attributes.type', ['ip-src', 'ip-dst', 'ip']])
                    ->andWhere(['misp_attributes.disable_correlation' => false])
                    ->all();
                
                foreach ($hits as $hit) {
                    $mispMatch = true;
                    
                    $ipType = array_search($hit->value, $ipsToCheck);
                    if ($ipType !== false) {
                        $threatLevel = $hit->mispEvent->threat_level ?? 4;
                        
                        $hitInfo .= " misp_{$ipType}_hit=true"
                                . " misp_{$ipType}_id=" . $hit->attribute_id
                                . " misp_{$ipType}_tl=" . $threatLevel;
                                . " misp_attr_id=" . $hit->attribute_id;
                        
                        $isSecmon = false;
                        if ($hit->mispEvent && $hit->mispEvent->tags) {
                            $eventTags = @json_decode($hit->mispEvent->tags, true);
                            if (is_array($eventTags) && in_array('secmon', $eventTags)) {
                                $isSecmon = true;
                            }
                        }
                        if ($isSecmon) {
                            $hitInfo .= " misp_{$ipType}_secmon=true";
                        }
                    }
                }
            }

            if ($mispMatch) {
                $rawPos = strpos($msg, " rawEvent=");
                if ($rawPos !== false) {
                    $msg = substr($msg, 0, $rawPos) 
                        . $hitInfo 
                        . substr($msg, $rawPos);
                } else {
                    $msg = rtrim($msg) . $hitInfo;
                }
            }

            if ($save_to_db) {
                $event = SecurityEvents::extractCefFields($msg, 'normalized');
                if ($event->save()) {
                    $sendSocket->send($event->id . ':' . $msg, ZMQ::MODE_NOBLOCK);
                    echo "Zapisane:" . $msg . PHP_EOL;
                }
            } else {
                $sendSocket->send($msg, ZMQ::MODE_NOBLOCK);
            }
        }
    }

    /** Opens a file for non-blocking reading. */
    private function openNonBlockingStream($file)
    {
        $stream = fopen($file, 'r+');
        if ($stream === false) {
            return null;
        }
        stream_set_blocking($stream, false);
        return $stream;
    }

    /** Extracts a field value from normalized event message. */
    private function extractField($msg, $field)
    {
        $pos = strpos($msg, $field);
        if ($pos === false) {
            return null;
        }
        $start = $pos + strlen($field);
        $end = strpos($msg, " ", $start);
        if ($end === false) {
            return substr($msg, $start);
        }
        return substr($msg, $start, $end - $start);
    }

    /** Removes port suffix and validates IP address. */
    private function normalizeIp($ip)
    {
        $ip = trim($ip);
        
        if (strpos($ip, ':') !== false && substr_count($ip, '.') == 3) {
            $ip = explode(':', $ip)[0];
        }
        
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return $ip;
        }
        
        return null;
    }
}