<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\Exception;
use ZMQ;
use ZMQContext;
use ZMQSocketException;

require '/var/www/html/secmon/vendor/autoload.php';

class AggregatorController extends Controller
{
    public function actionIndex()
    {
        $aggregator_config_file = $this->openNonBlockingStream("/var/www/html/secmon/config/aggregator_config.ini");

        if ($aggregator_config_file) {
            while (($line = fgets($aggregator_config_file)) !== false) {
                if (strpos($line, "Log_input:") !== FALSE) {
                    $parts = explode(":", $line);
                    $logPath = trim($parts[1]);
                }

                if (strpos($line, "Name:") !== FALSE) {
                    $parts = explode(":", $line);
                    $deviceName = trim($parts[1]);
                }

                if (strpos($line, "Nor_input_NP:") !== FALSE) {
                    $parts = explode(":", $line);
                    $normInputFile = trim($parts[1]);
                }

                if (strpos($line, "Nor_output_NP:") !== FALSE) {
                    $parts = explode(":", $line);
                    $normOutputFile = trim($parts[1]);
                }

                if (strpos($line, "Aggregator:") !== FALSE) {
                    $parts = explode(":", $line);
                    $port = trim($parts[1]);
                }
            }
        }
        fclose($aggregator_config_file);

        if ($logPath == null || $deviceName == null) {
            throw new Exception('Not all arguments were specified');
        }

        if (!is_dir($logPath)) {
            throw new Exception('Log path is not directory' . $logPath);
        }

        if (!is_numeric($port)) {
            throw new Exception('Port is not a numeric value');
        }

        $zmq = new ZMQContext();
        $socket = $zmq->getSocket(ZMQ::SOCKET_PUSH);
        $socket->connect("tcp://secmon_normalizer:" . $port);

        $streamPosition = [];
        $streams = [];
        date_default_timezone_set("Europe/Bratislava");
        echo "[" . date("Y-m-d H:i:s") . "] Aggregator started!" . PHP_EOL;

        while (1) {
            $this->openStreams($streams, $logPath, [$normOutputFile, $normInputFile]);

            foreach ($streams as $file => $stream) {
                if (!array_key_exists($file, $streamPosition)) {
                    $pathToFile = $logPath . "/" . $file;
                    $endOfFilePos = intval(exec("wc -c '$pathToFile'"));
                    $streamPosition[$file] = $endOfFilePos;
                }
                usleep(100000); // nutne kvoli vytazeniu CPU (0.1 sekunda)
                clearstatcache(false, $logPath . "/" . $file);
                fseek($stream, $streamPosition[$file]);
                while (($line = fgets($stream)) != FALSE) {
                    if (!empty($line)) {
                        $socket->send($line, ZMQ::MODE_DONTWAIT);
                        flush();
                    }
                }

                $streamPosition[$file] = ftell($stream);
            }

        }
    }

    function openStreams(&$streams, $path, $exclude = [])
    {
        $files = scandir($path);

        foreach ($files as $file) {
            $fullPath = $path . '/' . $file;

            if ($file == '..' || $file == '.' || in_array($fullPath, $exclude)) {
                continue;
            }

            if (is_dir($fullPath)) {
                $this->getFilesFromSubDirectory($fullPath, $file, $streams);
                continue;
            }

            if (array_key_exists($file, $streams)) {
                continue;
            }

            $stream = $this->openNonBlockingStream($fullPath);

            if ($stream == null) {
                echo 'Cannot open file ' . $fullPath . PHP_EOL;

                continue;
            }

            $streams[$file] = $stream;
        }
    }

    function getFilesFromSubDirectory($dirPath, $dirName, &$streams)
    {

        $files = scandir($dirPath);

        foreach ($files as $file) {
            $fullPath = $dirPath . '/' . $file;
            $index = $dirName . '/' . $file;

            if ($file != "messages" && $file != 'secure') {
                continue;
            }

            if (array_key_exists($index, $streams)) {
                continue;
            }

            $stream = $this->openNonBlockingStream($fullPath);

            if ($stream == null) {
                echo 'Cannot open file ' . $fullPath . PHP_EOL;
                continue;
            }

            $streams[$index] = $stream;
        }
    }

    function openNonBlockingStream($file)
    {
        $stream = fopen($file, 'r+');

        if ($stream === false) {
            return null;
        }

        stream_set_blocking($stream, false);

        return $stream;
    }

}