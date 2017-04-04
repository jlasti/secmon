<?php
namespace app\commands;

use app\models\Event;
use app\models\Event\Normalized;
use Yii;
use yii\console\Controller;
use yii\console\Exception;

class CorrelatorController extends Controller
{
	public function actionIndex($logPath, $deviceName)
	{
		if($logPath == null || $deviceName == null)
		{
			throw new Exception('Not all arguments were specified');
		}

		if(!is_dir($logPath))
		{
			throw new Exception('Log path is not directory');
		}

		$normOutputFile = $logPath . '/__secOutput';
		$normInputFile = $logPath . '/__secInput';

		$corrOutputFile = Yii::getAlias('@app/__secOutput');
		$corrInputFile = Yii::getAlias('@app/__secInput');

		$streams = [];

		$normOutputStream = $this->openPipe($normOutputFile);
		$normInputStream = $this->openPipe($normInputFile);

		$corrOutputStream = $this->openPipe($corrOutputFile);
		$corrInputStream = $this->openPipe($corrInputFile);

		if($normOutputStream == null || $normInputStream == null || $corrOutputStream == null || $corrInputStream == null)
		{
			$msg = 'Cannot open SEC pipes' . PHP_EOL;
			$msg .= 'Normalizer Output: ' . ($normOutputStream == null ? 'error' : 'open') . PHP_EOL;
			$msg .= 'Normalizer Input: ' . ($normInputStream == null ? 'error' : 'open') . PHP_EOL;
			$msg .= 'Global SEC output: ' . ($corrOutputStream == null ? 'error' : 'open') . PHP_EOL;
			$msg .= 'Global SEC input: ' . ($corrInputStream == null ? 'error' : 'open') . PHP_EOL;

			throw new Exception($msg);
		}

		while(1)
		{
			$this->openStreams($streams, $logPath, [$normOutputFile, $normInputFile]);

			foreach($streams as $file => $stream)
			{
				$line = fgets($stream);

				if(!empty($line))
				{
					echo '|';

					fwrite($normOutputStream, $line);
				}
			}

			$line = fgets($normInputStream);

			if(!empty($line))
			{
				echo '$';

				Yii::info(sprintf("Normalized:\n%s\n", $line));

				$event = Normalized::fromCef($line);

				if($event->save())
				{
					fwrite($corrOutputStream, $event->id . ':' . $line);
				}
			}

			$line = fgets($corrInputStream);

			if(!empty($line))
			{
				echo '%';

				Yii::info(sprintf("Correlated:\n%s\n", $line));

				$event = Event::fromCef($line);

				$event->save();
			}
		}
	}

	function openStreams(&$streams, $path, $exclude = [])
	{
		$files = scandir($path);

		foreach($files as $file)
		{
			$fullPath = $path . '/' . $file;

			if($file == '..' || $file == '.' || in_array($fullPath, $exclude))
			{
				continue;
			}

			if(array_key_exists($file, $streams))
			{
				continue;
			}

			$stream = $this->openNonBlockingStream($fullPath);

			if($stream == null)
			{
				echo 'Cannot open file ' . $fullPath . PHP_EOL;

				continue;
			}

			$streams[$file] = $stream;
		}
	}

	function openNonBlockingStream($file)
	{
		$stream = fopen($file, 'r+');

		if($stream === false)
		{
			return null;
		}

		stream_set_blocking($stream, false);

		return $stream;
	}

	function openPipe($file)
	{
		$pipe = posix_mkfifo($file, 0666);

		return fopen($file, 'r+');
	}
}