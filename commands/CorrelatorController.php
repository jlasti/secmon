<?php
namespace app\commands;

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

		$secOutputFile = $logPath . '/__secOutput';
		$secInputFile = $logPath . '/__secInput';

		$streams = [];

		$secOutputStream = $this->openPipe($secOutputFile);
		$secInputStream = $this->openPipe($secInputFile);

		if($secOutputStream == null || $secInputStream == null)
		{
			$msg = 'Cannot open SEC pipes' . PHP_EOL;
			$msg .= 'Output: ' . ($secOutputStream == null ? 'error' : 'open') . PHP_EOL;
			$msg .= 'Input: ' . ($secInputStream == null ? 'error' : 'open') . PHP_EOL;

			throw new Exception($msg);
		}

		while(1)
		{
			$this->openStreams($streams, $logPath, [$secOutputFile, $secInputFile]);

			foreach($streams as $file => $stream)
			{
				$line = fgets($stream);

				if(!empty($line))
				{
					fwrite($secOutputStream, $line);
				}
			}

			$line = fgets($secInputStream);

			if(!empty($line))
			{
				$this->saveToDatabase($line);
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

	function saveToDatabase($line)
	{
		//TODO: implementovat
	}
}