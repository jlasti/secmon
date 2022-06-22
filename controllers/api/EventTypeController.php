<?php
namespace app\controllers\api;

use Yii;

class EventTypeController extends \yii\rest\ActiveController
{
    public $modelClass = 'app\models\Event\EventType';
}