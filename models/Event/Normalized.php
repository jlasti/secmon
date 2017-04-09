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

		$correlated = Event::fromCef($cefString);

		$event->cef_version = $correlated->cef_version;
		$event->host = $correlated->host;
		$event->datetime = $correlated->datetime;

		$event->cef_vendor = $correlated->cef_vendor;
		$event->cef_dev_prod = $correlated->cef_dev_prod;
		$event->cef_dev_version = $correlated->cef_dev_version;
		$event->cef_event_class_id = $correlated->cef_event_class_id;
		$event->cef_name = $correlated->cef_name;
		$event->cef_severity = $correlated->cef_severity;

		$data = explode('|', $cefString);

		for($i = 0; $i < 7; $i++)
		{
			array_shift($data);
		}

		$data = array_shift($data);
	
		preg_match('/rawEvent=(.*)/', $data, $matches);

		$raw = "";
		if(empty($matches))
		{
			$raw = null;
		}
		else
		{
			$raw = $matches[1];
		}

		$exData = explode(" ", $data);
		$values = [];
		foreach($exData as $val) {
			$tmp = explode("=", $val);
			if($tmp[0] == "rawEvent")
				break;
			$values[$tmp[0]] = $tmp[1];
		}

		$event->src_ip = $values['src'] ?? null;
		$event->dst_ip = $values['dst'] ?? null;
		$event->src_mac = $values['smac'] ?? null;
		$event->dst_mac = $values['dmac'] ?? null;
		$event->src_port = $values['spt'] ?? null;
		$event->dst_port = $values['dpt'] ?? null;
		$event->protocol = $values['proto'] ?? $values['app'] ?? null;
		$event->raw = $raw;

		return $event;
	}
}