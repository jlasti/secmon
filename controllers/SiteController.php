<?php

namespace app\controllers;

use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\forms\LoginForm;

class SiteController extends Controller
{
	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'logout' => ['post'],
				],
			],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function actions()
	{
		return [
			'error' => [
				'class' => 'yii\web\ErrorAction',
			],
		];
	}

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

	/**
	 * Displays login form and eventually logs User is
	 *
	 * @return string|\yii\web\Response
	 */
    public function actionLogin()
	{
		if(!Yii::$app->user->isGuest)
		{
			return $this->goHome();
		}

		$model = new LoginForm();

		if($model->load(Yii::$app->request->post()) && $model->login())
		{
			return $this->goBack();
		}

		return $this->render('login', [
			'model' => $model,
		]);
	}

	/**
	 * Logs user out
	 *
	 * @return \yii\web\Response
	 */
	public function actionLogout()
	{
		Yii::$app->user->logout();

		return $this->goHome();
	}

    /**
     *  Inits admin user
     *
     * @return void
     */
    public function actionInit()
    {
        $user = User::findByUsername('admin');
        if ($user === null)
        {
            $model = new User();
            $model->first_name = 'Admin';
            $model->last_name = '';
            $model->username = 'admin';
            $model->password = '$2y$13$SStnQKWo6hkm3Ka9cCNeROJ5hZkkQhHF3B3/Si/Gebln3PTLoAeHm';
            $model->rolesList = [1, 100];
            $model->email = 'admin@secmon.com';
            $model->save();
            echo 'done';
        }
        else
            echo 'user allready exists';
    }
}
