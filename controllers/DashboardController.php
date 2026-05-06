<?php

namespace app\controllers;

use Yii;
use app\models\User;
use yii\web\Controller;


class DashboardController extends Controller
{

    public $layout = 'dashboardLayout';

    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        
        // That's it! No auth_key extraction needed
        return $this->render('index');
    }
}
