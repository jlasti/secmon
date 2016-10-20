<?php
namespace app\controllers\api;

use Yii;

class EventController extends \yii\rest\ActiveController
{
    public $modelClass = 'app\models\Event\Event';
}