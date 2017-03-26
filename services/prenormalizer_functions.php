<?php
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

		$stream = openNonBlockingStream($fullPath);

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