<?php
namespace app\models\Event;

use app\models\Event;

class Normalized extends Event
{
	public static function tableName()
	{
		return 'events_normalized';
	}

	//preboha co to pisem
	//TODO: refaktor
	public static function fromCef($cefString)
	{
		$event = new static();

		$data = explode('|', $cefString);

		$dateHost = explode(' ', strrev(array_shift($data)), 3);

		$event->cef_version = str_replace('CEF:', '', strrev(array_shift($dateHost)));
		$event->host = strrev(array_shift($dateHost));
		$event->dateTime = strrev(array_shift($dateHost));

		$event->cef_vendor = array_shift($data);
		$event->cef_dev_prod = array_shift($data);
		$event->cef_dev_version = array_shift($data);
		$event->cef_event_class_id = array_shift($data);
		$event->cef_name = array_shift($data);
		$event->cef_severity = array_shift($data);

		preg_match_all('/\s*([^=]+)=(\S+)\s*/', array_shift($data), $matches);

		$values = array_combine($matches[0], $matches[1]);

		$event->src_ip = $matches['src'] ?? null;
		$event->dst_ip = $matches['dst'] ?? null;
		$event->src_port = $matches['spt'] ?? null;
		$event->dst_port = $matches['dpt'] ?? null;
		$event->protocol = $matches['proto'] ?? $matches['app'] ?? null;
		$event->raw = $matches['rawEvent'] ?? null;

		return $event;
	}
}