<?php
require_once 'prenormalizer_functions.php';

$logPath = $argv[1] ?? null;
$deviceName = $argv[2] ?? null;

if($logPath == null || $deviceName == null)
{
	echo 'Not all arguments were specified' . PHP_EOL;

	exit(1);
}

if(!is_dir($logPath))
{
	echo 'Log path is not directory' . PHP_EOL;

	exit(2);
}

$secOutputFile = $logPath . '/__secOutput';
$secInputFile = $logPath . '/__secInput';

$streams = [];

$secOutputStream = openPipe($secOutputFile);
$secInputStream = openPipe($secInputFile);

if($secOutputStream == null || $secInputStream == null)
{
	echo 'Cannot open SEC pipes' . PHP_EOL;
	echo 'Output: ' . ($secOutputStream == null ? 'error' : 'open') . PHP_EOL;
	echo 'Input: ' . ($secInputStream == null ? 'error' : 'open') . PHP_EOL;

	exit(3);
}

while(1)
{
	openStreams($streams, $logPath, [$secOutputFile, $secInputFile]);

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
		saveToDatabase($line);
	}
}