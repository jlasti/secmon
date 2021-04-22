<?php
namespace app\commands;

use app\models\Event;

class SeverityCalculator {

    public static function calculateSeverity($event)
    {
	$filepath = "/var/www/html/secmon/config/SeverityConfig.json";
	
	if (file_exists($filepath)) {
	    $json_string = file_get_contents($filepath);
	    $jsons = json_decode($json_string, true);
	
	    if ($jsons['active']) {
	        foreach ($jsons['exceptions'] as $json) {
	            foreach ($json['exception'] as $exception) {
	                if ($event[$exception['name']] != $exception['value']) {
		            continue 2;
		        }
	            }
	            return $json['severity'];
	        }
	    }	
	}

	$pri = $event->cef_severity;
        $rsyslogSeverity = $pri % 8;
        $rsyslogFacility = ($pri - $rsyslogSeverity) / 8;
        return 8 - $rsyslogSeverity;
    }
}
