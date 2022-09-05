<?php 

namespace app\commands;

//use app\models\EventsNormalized;
use app\models\SecurityEvents;
use Yii;
use yii\console\Controller;
use yii\console\Exception;
use ZMQ;
use ZMQContext;
use ZMQSocketException;

require '/var/www/html/secmon/vendor/autoload.php';


class NormalizerController extends Controller{

	public function actionIndex(){

		$aggregator_config_file = $this->openNonBlockingStream("/var/www/html/secmon/config/aggregator_config.ini");
		$save_to_db = false;				#boolen value to determine whether to execute save
		$module_loaded = false;			#variable used for reading line after Normalizer in config file
		$next_module = "correlator";
		if($aggregator_config_file){
			while(($line = fgets($aggregator_config_file)) !== false){
				if(strpos($line, "Log_input:") !== FALSE){
					$parts = explode(":", $line);
					$logPath = trim($parts[1]);
				}

				if(strpos($line, "Nor_input_NP:") !== FALSE){
					$parts = explode(":", $line);
					$normInputFile = trim($parts[1]);
				}

				if(strpos($line, "Nor_output_NP:") !== FALSE){
					$parts = explode(":", $line);
					$normOutputFile = trim($parts[1]);
				}

				if($module_loaded == true ){
					$parts = explode(":", $line);
					$next_module = strtolower(trim($parts[0]));
					$module_loaded = false;
				}

				if(strpos($line, "Normalizer:") !== FALSE){
					$parts = explode(":", $line);
					$portOut = trim($parts[1]);
					$module_loaded = true;	
				}
			}
		}else{
			throw new Exception('Not all arguments were specified');
		}
		fclose($aggregator_config_file);
		$portIn = $portOut - 1;			#calculate output port
		$aggregator_config_file = escapeshellarg("/var/www/html/secmon/config/aggregator_config.ini");
		$last_line = `tail -n 1 $aggregator_config_file`; 		#get last line of temp file

		if(strpos($last_line, "Normalizer:") !== FALSE){		#if last is normalizer, then ensure saving event to db
			$save_to_db = true;
		}

		if ($logPath == null) {
			throw new Exception('Not all arguments were specified');
		}
		
		if (!is_dir($logPath)) {
			throw new Exception('Log path is not directory');
		}

		if (!is_numeric($portIn) || !is_numeric($portOut)) {
			throw new Exception('One of ports is not a numeric value' . $portIn . strlen($portIn));
		}


		$normOutputStream = $this->openPipe($normOutputFile);
		$normInputStream = $this->openPipe($normInputFile);


		if ($normOutputStream == null || $normInputStream == null) {
			$msg = 'Cannot open SEC pipes' . PHP_EOL;
			$msg .= 'Normalizer Output: ' . ($normOutputStream == null ? 'error' : 'open') . PHP_EOL;
			$msg .= 'Normalizer Input: ' . ($normInputStream == null ? 'error' : 'open') . PHP_EOL;

			throw new Exception($msg);
		}

		$zmq = new ZMQContext();
		$recSocket = $zmq->getSocket(ZMQ::SOCKET_PULL);  
		$recSocket->bind("tcp://*:" . $portIn);
		
		$sendSocket = $zmq->getSocket(ZMQ::SOCKET_PUSH);
		$sendSocket->connect("tcp://secmon_" . $next_module . ":" . $portOut);

		date_default_timezone_set("Europe/Bratislava");
		echo "[" . date("Y-m-d H:i:s") . "] Worker normalizer started!" . PHP_EOL;

		while(true){
			$msg = $recSocket->recv(ZMQ::MODE_NOBLOCK);
			if(empty($msq)){
				usleep(30000);
			}

			if (!empty($msg)) {
				//print($msg);
				fwrite($normInputStream, $msg);
				#echo "Zapisane:" . $msg . PHP_EOL;
				flush();
			}

			while (($line = fgets($normOutputStream)) != FALSE) {
				if (!empty($line)) {
					Yii::info(sprintf("Normalized:\n%s\n", $line));
					if($save_to_db){
						$event = SecurityEvents::extractCefFields($line, 'normalized');
						if ($event->save()) {
							$sendSocket->send($event->id . ':' . $line, ZMQ::MODE_NOBLOCK);
						}	
					} else {
						$sendSocket->send($line, ZMQ::MODE_NOBLOCK);

					}
				} 
			}
		}
	}

	function openPipe($file){
		$pipe = posix_mkfifo($file, 0666);
		$openPipe = fopen($file, 'r+');
		stream_set_blocking($openPipe, false);

		return $openPipe;
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

