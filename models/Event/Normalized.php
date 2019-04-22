<?php
namespace app\models\Event;

use app\models\Event;
use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use MaxMind\Db\Reader\InvalidDatabaseException;

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
            $values[$tmp[0]] = isset($tmp[1]) ? $tmp[1] : "";
		}

		$event->src_ip = $values['src'] ?? "";
		$event->dst_ip = $values['dst'] ?? "";
		$event->src_mac = $values['smac'] ?? "";
		$event->dst_mac = $values['dmac'] ?? "";
		$event->src_port = $values['spt'] ?? "";
		$event->dst_port = $values['dpt'] ?? "";
		$event->protocol = $values['proto'] ?? $values['app'] ?? "";
		$event->raw = $raw;

		self::setEventLoc($event);

		return $event;
	}

    /**
     * @param $hostname
     * @return \GeoIp2\Model\City
     * @throws \MaxMind\Db\Reader\InvalidDatabaseException
     * @throws \GeoIp2\Exception\AddressNotFoundException
     */
    private static function getGeoLocationLib($hostname) {
        $reader = new Reader('/usr/local/share/GeoIP/GeoLite2-City.mmdb');
        $record = $reader->city($hostname);
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
            $geoLocationLib = null;
            try {
                $geoLocationLib = self::getGeoLocationLib($event->src_ip);
            } catch (AddressNotFoundException $e) {
                echo 'Message: ' .$e->getMessage();
            } catch (InvalidDatabaseException $e) {
                echo 'Message: ' .$e->getMessage();
            }
            /** @var \GeoIp2\Model\City $geoLocationLib */
            if ($geoLocationLib) {
                $event->src_code = $geoLocationLib->country->isoCode ?? "";
                $event->src_country = $geoLocationLib->country->name ?? "";
                $event->src_city = $geoLocationLib->city->name ?? "";
                $event->src_latitude = $geoLocationLib->location->latitude ?? "";
                $event->src_longitude = $geoLocationLib->location->longitude ?? "";
            } else {
                $geoLocationApi = self::getGeoLocationApi($event->src_ip);
                if ($geoLocationApi) {
                    $event->src_code = $geoLocationLib["country"] ?? "";
                    $event->src_city = $geoLocationLib["city"] ?? "";
                    /** @var array $latlon */
                    $latlon = explode(",", $geoLocationLib["loc"]);
                    if (count($latlon) > 1) {
                        $event->src_latitude = $latlon[0] ?? "";
                        $event->src_longitude = $latlon[1] ?? "";
                    }
                }

            }
        }

        if ($event->dst_ip) {
            $geoLocationLib = null;
            try {
                $geoLocationLib = self::getGeoLocationLib($event->dst_ip);
            } catch (AddressNotFoundException $e) {
                echo 'Message: ' .$e->getMessage();
            } catch (InvalidDatabaseException $e) {
                echo 'Message: ' .$e->getMessage();
            }
            /** @var \GeoIp2\Model\City $geoLocationLib */
            if ($geoLocationLib) {
                $event->dst_code = $geoLocationLib->country->isoCode ?? "";
                $event->dst_country = $geoLocationLib->country->name ?? "";
                $event->dst_city = $geoLocationLib->city->name ?? "";
                $event->dst_latitude = $geoLocationLib->location->latitude ?? "";
                $event->dst_longitude = $geoLocationLib->location->longitude ?? "";
            } else {
                $geoLocationApi = self::getGeoLocationApi($event->dst_ip);
                if ($geoLocationApi) {
                    $event->dst_code = $geoLocationLib["country"] ?? "";
                    $event->dst_city = $geoLocationLib["city"] ?? "";
                    /** @var array $latlon */
                    $latlon = explode(",", $geoLocationLib["loc"]);
                    if (count($latlon) > 1) {
                        $event->dst_latitude = $latlon[0] ?? "";
                        $event->dst_longitude = $latlon[1] ?? "";
                    }
                }

            }
        }
    }
}