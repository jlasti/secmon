<?php

namespace app\commands;

//use app\models\EventsCorrelated;
use app\models\SecurityEvents;
use Yii;
use yii\console\Controller;
use yii\console\Exception;
use ZMQ;
use ZMQContext;
use ZMQSocketException;

require '/var/www/html/secmon/vendor/autoload.php';

class CorrelatorController extends Controller
{

	public function actionIndex()
	{

		$aggregator_config_file = $this->openNonBlockingStream("/var/www/html/secmon/config/aggregator_config.ini");

		if ($aggregator_config_file) {
			while (($line = fgets($aggregator_config_file)) !== false) {
				if (strpos($line, "Cor_input_NP:") !== FALSE) {
					$parts = explode(":", $line);
					$corrInputFile = trim($parts[1]);
				}

				if (strpos($line, "Cor_output_NP:") !== FALSE) {
					$parts = explode(":", $line);
					$corrOutputFile = trim($parts[1]);
				}
			}
		}
		fclose($aggregator_config_file);
		$aggregator_config_file = escapeshellarg("/var/www/html/secmon/config/aggregator_config.ini");
		$last_line = `tail -n 1 $aggregator_config_file`;
		$parts = explode(":", $last_line);
		$port = trim($parts[1]);

		$corrOutputStream = $this->openPipe($corrOutputFile);
		$corrInputStream = $this->openPipe($corrInputFile);

		if ($corrOutputStream == null || $corrInputStream == null) {
			$msg = 'Cannot open SEC pipes' . PHP_EOL;
			$msg .= 'Global SEC output: ' . ($corrOutputStream == null ? 'error' : 'open') . PHP_EOL;
			$msg .= 'Global SEC input: ' . ($corrInputStream == null ? 'error' : 'open') . PHP_EOL;

			throw new Exception($msg);
		}

		$zmq = new ZMQContext();
		$recSocket = $zmq->getSocket(ZMQ::SOCKET_PULL);
		$recSocket->bind("tcp://*:" . $port);

		date_default_timezone_set("Europe/Bratislava");
		echo "[" . date("Y-m-d H:i:s") . "] Worker correlator started!" . PHP_EOL;

		while (true) {
			$msg = $recSocket->recv(ZMQ::MODE_NOBLOCK);
			if (empty($msq)) {
				usleep(30000);
			}

			if (!empty($msg)) {
				//echo "Received Message:" . $msg . PHP_EOL;
				fwrite($corrInputStream, $msg);
				flush();
			}

			while (($line = fgets($corrOutputStream)) != FALSE) {
				if (!empty($line)) {
					Yii::info(sprintf("Correlated:\n%s\n", $line));
					$event = SecurityEvents::extractCefFields($line, 'correlated');
					$event->save();
				}
			}
		}
	}

	function openPipe($file)
	{
		$pipe = posix_mkfifo($file, 0666);
		$openPipe = fopen($file, 'r+');
		stream_set_blocking($openPipe, false);

		return $openPipe;
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

?>