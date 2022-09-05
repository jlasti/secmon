<?php
namespace app\models\Event;

use app\models\Event;
use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use MaxMind\Db\Reader\InvalidDatabaseException;
use app\commands\SeverityCalculator;

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
		
		//nevyhnutne dva if-y, lebo delimiter " " a "=" kazia vstup do atributov request_url a request_client_application
		if(preg_match('/request=/', $data)){
			$start_pos = strpos($data, 'request=');
			$start_pos += strlen('request=');
			$end_pos = strpos($data, ' ', $start_pos);
			$length = $end_pos - $start_pos;
			$request_url = substr($data, $start_pos, $length);
		}
		if(preg_match('/requestClientApplication=/', $data)){
			$start_pos = strpos($data, 'requestClientApplication=');
			$start_pos += strlen('requestClientApplication=');
			$end_pos = strpos($data, ' rawEvent=', $start_pos);
			$length = $end_pos - $start_pos;
			$request_client_application = substr($data, $start_pos, $length);
		}	

		$event->src_ip = $values['src'] ?? "";
		if($event->src_ip == "localhost"){
			$event->src_ip = "127.0.0.1";
		}
		$event->dst_ip = $values['dst'] ?? "";
		$event->src_mac = $values['smac'] ?? "";
		$event->dst_mac = $values['dmac'] ?? "";
		$event->src_port = $values['spt'] ?? "";
		$event->dst_port = $values['dpt'] ?? "";
		$event->protocol = $values['proto'] ?? $values['app'] ?? "";
		$event->request_method = $values['request_method'] ?? "";
		$event->request_url = $request_url ?? "";
		$event->request_client_application = $request_client_application ?? "";
		$event->destination_user_name = $values['duser'] ?? "";
		$event->destination_user_id = $values['duid'] ?? "";
		$event->destination_group_name = $values['cs1'] ?? "";
		$event->destination_group_id = $values['cn1'] ?? "";
		$event->device_process_id = $values['dvcpid'] ?? "";
		$event->source_user_privileges = $values['spriv'] ?? "";
		$event->exec_user = $values['cs2'] ?? "";
		$event->raw = $raw;

		//map netwrok model for src IP
		$position = strpos($cefString, "src_network_model_id=");
		if($position != FALSE){
				$event->src_ip_network_model = $cefString[$position + strlen("src_network_model_id=")];
		}

		//map network model for dst IP
		$position = strpos($cefString, "dst_network_model_id=");
		if($position != FALSE){
				$event->dst_ip_network_model = $cefString[$position + strlen("dst_network_model_id=")];
		}
		
		//map geoIP for src IP
		$position = strpos($cefString, "src_country_isoCode=");
		if($position != FALSE){
				$start_position = $position + strlen("src_country_isoCode=");
				$end_position = strpos($cefString, " ", $start_position);
				$event->src_code = substr($cefString, $start_position, $end_position - $start_position);
		}
		

		$position = strpos($cefString, "src_country_name=");
		if($position != FALSE){
				$start_position = $position + strlen("src_country_name=");
				$end_position = strpos($cefString, "src_", $start_position) - 1;
				$event->src_country = substr($cefString, $start_position, $end_position - $start_position);
		}

		$position = strpos($cefString, "src_city_name=");
		if($position != FALSE){
				$start_position = $position + strlen("src_city_name=");
				$end_position = strpos($cefString, "src_", $start_position) - 1;
				$event->src_city = substr($cefString, $start_position, $end_position - $start_position);
		}

		$position = strpos($cefString, "src_location_latitude=");
		if($position != FALSE){
				$start_position = $position + strlen("src_location_latitude=");
				$end_position = strpos($cefString, " ", $start_position);
				$event->src_latitude = substr($cefString, $start_position, $end_position - $start_position);
		}

		$position = strpos($cefString, "src_location_longitude=");
		if($position != FALSE){
				$start_position = $position + strlen("src_location_longitude=");
				$end_position = strpos($cefString, " ", $start_position);
				$event->src_longitude = substr($cefString, $start_position, $end_position - $start_position);
		}
		

		//map geoIP for dst IP
		$position = strpos($cefString, "dst_country_isoCode=");
		if($position != FALSE){
				$start_position = $position + strlen("dst_country_isoCode=");
				$end_position = strpos($cefString, " ", $start_position);
				$event->dst_code = substr($cefString, $start_position, $end_position - $start_position);
		}

		$position = strpos($cefString, "dst_country_name=");
		if($position != FALSE){
				$start_position = $position + strlen("dst_country_name=");
				$end_position = strpos($cefString, "dst_", $start_position) - 1;
				$event->dst_country = substr($cefString, $start_position, $end_position - $start_position);
		}

		$position = strpos($cefString, "dst_city_name=");
		if($position != FALSE){
				$start_position = $position + strlen("dst_city_name=");
				$end_position = strpos($cefString, "dst_", $start_position) - 1;
				$event->dst_city = substr($cefString, $start_position, $end_position - $start_position);
		}

		$position = strpos($cefString, "dst_location_latitude=");
		if($position != FALSE){
				$start_position = $position + strlen("dst_location_latitude=");
				$end_position = strpos($cefString, " ", $start_position);
				$event->dst_latitude = substr($cefString, $start_position, $end_position - $start_position);
		}

		$position = strpos($cefString, "dst_location_longitude=");
		if($position != FALSE){
				$start_position = $position + strlen("dst_location_longitude=");
				$end_position = strpos($cefString, " ", $start_position);
				$event->dst_longitude = substr($cefString, $start_position, $end_position - $start_position);
		}

		$event->cef_severity = SeverityCalculator::calculateSeverity($event);

		return $event;
	}
}
