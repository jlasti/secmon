<?php
namespace app\models\Event;

use app\models\Event;

class Normalized extends Event
{
	public static function tableName()
	{
		return 'events_normalized';
	}

	public static function fromCef($cefString)
	{
		//TODO: implement
	}
}