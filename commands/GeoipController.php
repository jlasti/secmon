<?php 

namespace app\commands;

use app\models\Event;
use app\models\Event\Normalized;
use Yii;
use yii\console\Controller;
use yii\console\Exception;
use ZMQ;
use ZMQContext;
use ZMQSocketException;
use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use MaxMind\Db\Reader\InvalidDatabaseException;

require '/var/www/html/secmon/vendor/autoload.php';


class GeoipController extends Controller{

	public function actionIndex(){

		$temp_config = $this->openNonBlockingStream("/var/www/html/secmon/config/aggregator_config.ini");
        $save_to_db = 0;
        $module_loaded = false;			#variable used for reading line after Geoip module in config file
		$next_module = "correlator";
		if($temp_config){
			while(($line = fgets($temp_config)) !== false){
				if($module_loaded == true ){
					$parts = explode(":", $line);
					$next_module = strtolower(trim($parts[0]));
					$module_loaded = false;
				}

                if(strpos($line, "Geoip:") !== FALSE){
					$parts = explode(":", $line);
					$portOut = trim($parts[1]);
                    $module_loaded = true;
				}
			}
		}else{
		     throw new Exception('Could not open a config file');
		}

        fclose($temp_config);
        $portIn = $portOut - 1;			#calculate input port
		$temp_config = escapeshellarg("/var/www/html/secmon/config/aggregator_config.ini");
		$last_line = `tail -n 1 $temp_config`; 		#get last line of temp file

		if(strpos($last_line, "Geoip:")!== FALSE){		
			$save_to_db = 1;
		}

        if (!is_numeric($portIn) || !is_numeric($portOut)) {
		    throw new Exception('One of ports is not a numeric value');
        }
        
        $zmq = new ZMQContext();
		$recSocket = $zmq->getSocket(ZMQ::SOCKET_PULL);  
		$recSocket->bind("tcp://*:" . $portIn);

		$sendSocket = $zmq->getSocket(ZMQ::SOCKET_PUSH);
        $sendSocket->connect("tcp://secmon-" . $next_module . ":" . $portOut);
        print("Geoip tcp://secmon-" . $next_module . ":" . $portOut);

        date_default_timezone_set("Europe/Bratislava");
        echo "[" . date("Y-m-d H:i:s") . "] Geoip module started!" . PHP_EOL;

        while(true){
            $srcIp = $dstIp = -1;
            $msg = $recSocket->recv(ZMQ::MODE_NOBLOCK);
            
			if(empty($msg)){
				usleep(30000);
			}else {

                $position1 = strpos($msg, "src=");
				if($position1 != FALSE){
					$position2 = strpos($msg, " ", $position1);
					$position3 = $position2 - $position1 - strlen("src=");
					$srcIp = substr($msg, $position1 + strlen("src="), $position3);
				}

				$position1 = strpos($msg, "dst=");
				if($position1 != FALSE){
					$position2 = strpos($msg, " ", $position1);
					$position3 = $position2 - $position1 - strlen("dst=");
					$dstIp = substr($msg, $position1 + strlen("dst="), $position3);
                }
               
                if($srcIp != -1) {
                    $geoLocationLib = null;
                    if($srcIp != "localhost" && $srcIp != "127.0.0.1" && strpos($srcIp, '10.0.') === false){
                        try {
                            $geoLocationLib = self::getGeoLocationLib($srcIp);
                        } catch (AddressNotFoundException $e) {
                            echo 'Message: ' .$e->getMessage();
                        } catch (InvalidDatabaseException $e) {
                            echo 'Message: ' .$e->getMessage();
                        }
                        /** @var \GeoIp2\Model\City $geoLocationLib */
                        if ($geoLocationLib) {
                            if($geoLocationLib->country->isoCode)
                                $msg = $msg . " src_country_isoCode=" . $geoLocationLib->country->isoCode;
                            
                            if($geoLocationLib->country->name)
                                $msg = $msg . " src_country_name=" . $geoLocationLib->country->name;
    
                            if($geoLocationLib->city->name)
                                $msg = $msg . " src_city_name=" . $geoLocationLib->city->name;
    
                            if($geoLocationLib->location->latitude)
                                $msg = $msg . " src_location_latitude=" . $geoLocationLib->location->latitude;
    
                            if($geoLocationLib->location->longitude)
                                $msg = $msg . " src_location_longitude=" . $geoLocationLib->location->longitude;
                        }
                        // osetrenie pre parsovanie v EventsNormalized, pri volani ::fromCef
                        $msg = $msg . " "; 
                    }
                }

                if($dstIp != -1) {
                    $geoLocationLib = null;
                    if($dstIp == "localhost" && $dstIp = "127.0.0.1" && !strpos($dstIp, '10.0.') === false){
                        try {
                            $geoLocationLib = self::getGeoLocationLib($dstIp);
                        } catch (AddressNotFoundException $e) {
                            echo 'Message: ' .$e->getMessage();
                        } catch (InvalidDatabaseException $e) {
                            echo 'Message: ' .$e->getMessage();
                        }
                        /** @var \GeoIp2\Model\City $geoLocationLib */
                        if ($geoLocationLib) {
                            if($geoLocationLib->country->isoCode)
                                $msg = $msg . " dst_country_isoCode=" . $geoLocationLib->country->isoCode;
                            
                            if($geoLocationLib->country->name)
                                $msg = $msg . " dst_country_name=" . $geoLocationLib->country->name;
    
                            if($geoLocationLib->city->name)
                                $msg = $msg . " dst_city_name=" . $geoLocationLib->city->name;
    
                            if($geoLocationLib->location->latitude)
                                $msg = $msg . " dst_location_latitude=" . $geoLocationLib->location->latitude;
    
                            if($geoLocationLib->location->longitude)
                                $msg = $msg . " dst_location_longitude=" . $geoLocationLib->location->longitude;
                        } 
                        $msg = $msg . " "; 
                    } 
                }
                //print($msg);
                
				if($save_to_db){
					$event = Normalized::fromCef($msg);
					if($event->save()) {
						$sendSocket->send($event->id . ':' . $msg, ZMQ::MODE_NOBLOCK);
					}	
				}else{
					$sendSocket->send($msg, ZMQ::MODE_NOBLOCK);
				}
				
			}    
		}
    }
    
     /**
     * @param $hostname
     * @return \GeoIp2\Model\City
     * @throws \MaxMind\Db\Reader\InvalidDatabaseException
     * @throws \GeoIp2\Exception\AddressNotFoundException
     */
    private static function getGeoLocationLib($hostname) {
        $reader = new Reader('/usr/local/share/GeoIP/GeoLite2-City.mmdb');
        $record = $reader->city($hostname);
        return $record ?? null;

    }
	
	function openNonBlockingStream($file){
		$stream = fopen($file, 'r+');

		if ($stream === false) {
			return null;
		}

		stream_set_blocking($stream, false);

		return $stream;
   }
}

?>

