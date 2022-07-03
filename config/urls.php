<?php
return [
	['class' => 'yii\rest\UrlRule', 'controller' => ['event' => 'api/event'], 'prefix' => 'api', 'pluralize' => false],
    ['class' => 'yii\rest\UrlRule', 'controller' => ['event-type' => 'api/event-type'], 'prefix' => 'api', 'pluralize' => false],
];