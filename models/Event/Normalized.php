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
			if($tmp[0] == "rawEvent" || $tmp[0] == "reason")
			{
				break;
			}
            $values[$tmp[0]] = isset($tmp[1]) ? $tmp[1] : null;
		}

		$event->src_ip = $values['src'] ?? null;
		$event->dst_ip = $values['dst'] ?? null;
		$event->src_mac = $values['smac'] ?? null;
		$event->dst_mac = $values['dmac'] ?? null;
		$event->src_port = $values['spt'] ?? null;
		$event->dst_port = $values['dpt'] ?? null;
		$event->protocol = $values['proto'] ?? $values['app'] ?? null;
		$event->raw = $raw;

		self::setEventLoc($event);

		return $event;
	}

    /**
     * @param $hostname
     * @return array|null
     */
    private static function getGeoLocationLib($hostname) {
        $record = geoip_record_by_name($hostname);
        return $record ?? null;

    }

    /**
     * @param $hostname
     * @return mixed|null
     */
    private  static function getGeoLocationApi($hostname) {
        $details = json_decode(file_get_contents("http://ipinfo.io/{$hostname}/json"));
        return $details ?? null;
    }

    /**
     * @param $event
     */
    private static function setEventLoc($event){
        if ($event->src_ip) {
            $geoLocationLib = self::getGeoLocationLib($event->src_ip);
            if ($geoLocationLib) {
                $event->src_code = $geoLocationLib["country_code"];
                $event->src_country = $geoLocationLib["country_name"];
                $event->src_city = $geoLocationLib["city"];
                $event->src_latitude = $geoLocationLib["latitude"];
                $event->src_longtitude = $geoLocationLib["longitude"];
            } else {
                $geoLocationApi = self::getGeoLocationApi($event->src_ip);
                if ($geoLocationApi) {
                    $event->src_code = $geoLocationLib["country"];
                    $event->src_city = $geoLocationLib["city"];
                    $latlon = explode(",", $geoLocationLib["loc"]);
                    if (count($latlon) > 1) {
                        $event->src_latitude = $latlon[0];
                        $event->src_longtitude = $latlon[1];
                    }
                }

            }
        }

        if ($event->dst_ip) {
            $geoLocationLib = self::getGeoLocationLib($event->dst_ip);
            if ($geoLocationLib) {
                $event->dst_code = $geoLocationLib["country_code"];
                $event->dst_country = $geoLocationLib["country_name"];
                $event->dst_city = $geoLocationLib["city"];
                $event->dst_latitude = $geoLocationLib["latitude"];
                $event->dst_longtitude = $geoLocationLib["longitude"];
            } else {
                $geoLocationApi = self::getGeoLocationApi($event->dst_ip);
                if ($geoLocationApi) {
                    $event->dst_code = $geoLocationLib["country"];
                    $event->dst_city = $geoLocationLib["city"];
                    $latlon = explode(",", $geoLocationLib["loc"]);
                    if (count($latlon) > 1) {
                        $event->dst_latitude = $latlon[0];
                        $event->dst_longtitude = $latlon[1];
                    }
                }

            }
        }
    }
}