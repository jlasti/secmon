<?php 

namespace app\commands;

use app\models\SecurityEvents;
use Yii;
use yii\console\Controller;
use yii\console\Exception;
use ZMQ;
use ZMQContext;
use ZMQSocketException;

require '/var/www/html/secmon/vendor/autoload.php';


class NetworkController extends Controller{

	public function actionIndex(){

		$aggregator_config_file = $this->openNonBlockingStream("/var/www/html/secmon/config/aggregator_config.ini");
		$save_to_db = 0;
		$module_loaded = false;			#variable used for reading line after Network model module in config file
		$next_module = "correlator";
		if($aggregator_config_file){
			while(($line = fgets($aggregator_config_file)) !== false){
				if($module_loaded == true ){
					$parts = explode(":", $line);
					$next_module = strtolower(trim($parts[0]));
					$module_loaded = false;
				}

				if(strpos($line, "Network_model:") !== FALSE){
					$parts = explode(":", $line);
					$port = trim($parts[1]);
					$module_loaded = true;
				}
			}
		}else{
		     throw new Exception('Could not open a config file');
		}

		$middleware_config_file = $this->openNonBlockingStream("/var/www/html/secmon/config/secmon_config.ini");
		if($middleware_config_file){
			while(($line = fgets($middleware_config_file)) !== false){
				if(strpos($line, "host =") !== FALSE){
					$parts = explode("=", $line);
					$host = trim($parts[1]);
				}
				if(strpos($line, "database =") !== FALSE){
					$parts = explode("=", $line);
					$database = trim($parts[1]);
				}
				if(strpos($line, "user =") !== FALSE){
					$parts = explode("=", $line);
					$user = trim($parts[1]);
				}
				if(strpos($line, "password =") !== FALSE){
					$parts = explode("=", $line);
					$password = trim($parts[1]);
				}
			}
		}else{
		     throw new Exception('Not all arguments were specified');
		}

		fclose($aggregator_config_file);
		fclose($middleware_config_file);
		$aggregator_config_file = escapeshellarg("/var/www/html/secmon/config/aggregator_config.ini");
		$last_line = `tail -n 1 $aggregator_config_file`; 		#get last line of temp file

		if(strpos($last_line, "Network_model:")!== FALSE){		#if last is network_model, then ensure saving event to db
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
		echo "[" . date("Y-m-d H:i:s") . "] Network model module started!" . PHP_EOL;

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

				//get hostname from log
				$array = explode(" ", $msg);
				$hostname = $array[4];
				
				//make lookup to db with source and destination address
				
				$srcNetworkDeviceId = $this->pairNetworkDevice($srcIp, $hostname, $host, $database, $user, $password);
				$dstNetworkDeviceId = $this->pairNetworkDevice($dstIp, $hostname, $host, $database, $user, $password);

				if($srcNetworkDeviceId != -1){
					$msg = $msg . "src_network_model_id=" . $srcNetworkDeviceId;
				}
				if($dstNetworkDeviceId != -1){
					$msg = $msg . "dst_network_model_id=" . $dstNetworkDeviceId;
				}
				//print($msg);
				if($save_to_db){
					$event = SecurityEvents::extractCefFields($msg, 'normalized');
					if($event->save()) {
						$sendSocket->send($event->id . ':' . $msg, ZMQ::MODE_NOBLOCK);
					}	
				}else{
					$sendSocket->send($msg, ZMQ::MODE_NOBLOCK);
				}
				
			}    
		}
	}

	function pairNetworkDevice($ip, $hostname, $host, $database, $user, $password){

		$connection = pg_connect("host=" . $host . " dbname=" . $database . " user=" . $user . " password=" . $password);
		
		if($connection ){

			if($ip == -1){
				$result = pg_query_params($connection, 'SELECT id from network_model where hostname = $1', array($hostname));
				if(pg_num_rows($result) > 0){
					$row = pg_fetch_row($result);
					pg_close($connection);
					return $row[0];
				}

				return -1;
			}

			if($ip == "localhost" || $ip == "127.0.0.1"){
				$result = pg_query_params($connection, 'SELECT id from network_model where hostname = $1', array($hostname));
				if(pg_num_rows($result) > 0){
					$row = pg_fetch_row($result);
					pg_close($connection);
					return $row[0];
				}
			}

			
			$result = pg_query_params($connection, 'SELECT id from network_model where ip_address = $1', array($ip));
			
			if(pg_num_rows($result) > 0){
				$row = pg_fetch_row($result);
				pg_close($connection);
				return $row[0];
			}else {
				$result = pg_query_params($connection, 'SELECT network_model_id from interface where ip_address = $1', array($ip));
				if(pg_num_rows($result) > 0){
					$row = pg_fetch_row($result);
					pg_close($connection);
					return $row[0];
				} else {
					pg_close($connection);
					return -1;
				}
			}
			
		}else{
			print("Error while connecting to database!");
			return -1;
		}
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

