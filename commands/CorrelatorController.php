<?php
namespace app\commands;

use app\models\Event;
use app\models\Event\Normalized;
use Yii;
use yii\console\Controller;
use yii\console\Exception;

require 'vendor/autoload.php';


class CorrelatorController extends Controller
{
    /**
     * @param $logPath
     * @param $deviceName
     * @throws Exception
     */
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

	$streamPosition = [];

	while(1)
	{
		$this->openStreams($streams, $logPath, [$normOutputFile, $normInputFile]);

		foreach($streams as $file => $stream)
		{

			if(!array_key_exists($file, $streamPosition))
				{
					$pathToFile = $logPath . "/" . $file;
					$endOfFilePos = intval(exec("wc -c '$pathToFile'"));
					$streamPosition[$file] = $endOfFilePos;
				}
				usleep(300000); // nutne kvoli vytazeniu CPU
				clearstatcache(false, $logPath . "/" . $file);
				fseek($stream, $streamPosition[$file]);

				while(($line = fgets($stream)) != FALSE)
				{
					if(!empty($line))
					{
						fwrite($normOutputStream, $line);
						flush();
					}
				}

				$streamPosition[$file] = ftell($stream);
			}
			
			while(($line = fgets($normInputStream)) != FALSE)
			{
				if(!empty($line))
				{
					Yii::info(sprintf("Normalized:\n%s\n", $line));

					$event = Normalized::fromCef($line);

					if($event->save())
					{
						fwrite($corrOutputStream, $event->id . ':' . $line);
					}
				}
			}
			
			while(($line = fgets($corrInputStream)) != FALSE)
			{
				if(!empty($line))
				{
					Yii::info(sprintf("Correlated:\n%s\n", $line));

					$event = Event::fromCef($line);
					$event->save();
				}
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

			if(is_dir($fullPath))
            {
			    $this->getFilesFromSubDirectory($fullPath, $file, $streams);
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

	function getFilesFromSubDirectory($dirPath, $dirName, &$streams)
    {

        $files = scandir($dirPath);

        foreach($files as $file)

        {
            $fullPath = $dirPath . '/' . $file;
            $index = $dirName . '/' . $file;

            if($file != "messages" && $file != 'secure')
            {
                continue;
            }

            if(array_key_exists($index, $streams))
            {
                continue;
            }

            $stream = $this->openNonBlockingStream($fullPath);

            if($stream == null)
            {
                echo 'Cannot open file ' . $fullPath . PHP_EOL;
                continue;
            }

            $streams[$index] = $stream;
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
		$openPipe = fopen($file, 'r+');
		stream_set_blocking($openPipe, false);
		
		return $openPipe;
	}
}
