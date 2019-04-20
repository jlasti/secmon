<?php
/**
 * Created by PhpStorm.
 * User: mkovac
 * Date: 13.1.2019
 * Time: 13:19
 */

namespace app\controllers\api;

use Yii;

class AnalyzeController extends \yii\rest\ActiveController
{
    public $modelClass = 'app\models\Event\EventAnalyzed';

}