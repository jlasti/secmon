<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\User\UserSearch;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
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
                    'delete' => ['POST'],
                ],
            ],
			'access' => [
				'class' => AccessControl::className(),
				'ruleConfig' => [
					'class' => 'app\components\AccessRule',
				],
				'rules' => [
					[
						'actions' => ['index', 'view'],
						'allow' => true,
						'roles' => ['view_users'],
					],
					[
						'actions' => ['create'],
						'allow' => true,
						'roles' => ['create_users'],
					],
					[
						'actions' => ['update'],
						'allow' => true,
						'roles' => ['update_users'],
					],
					[
						'actions' => ['delete'],
						'allow' => true,
						'roles' => ['delete_users'],
					],
                    [
                        'actions' => ['profile'],
                        'allow' => true,
                        'roles' => ['view_users']
                    ],
                    [
                        'actions' => ['init'],
                        'allow' => true
                    ]
				],
			],
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User(['scenario' => User::SCENARIO_CREATE]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
		$model->scenario = User::SCENARIO_UPDATE;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if($id != 1)
            $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Change password action.
     *
     * @return string|\yii\web\Response
     */
    public function actionProfile()
    {
        $model = new User();
        $model->scenario = User::SCENARIO_CHGPASS;

        if (Yii::$app->request->isPost)
        {
            $userId = Yii::$app->user->getId();
            $model = $this->findModel($userId);
            if ($model->load(Yii::$app->request->post()))
            {
                if (Yii::$app->getSecurity()->validatePassword($model->passwordTextOld, $model->password))
                {
                    if ($model->passwordTextNew === $model->passwordTextNew2)
                    {
                        $model->passwordText = $model->passwordTextNew;
                        if ($model->save())
                            return $this->redirect(Url::home());
                        else
                            $model->addError('passwordTextOld', 'Unexpected error while updating password');
                    }
                    else
                        $model->addError('passwordTextNew2', 'New passwords aren\'t same');
                }
                else
                    $model->addError('passwordTextOld', 'Wrong old password');
            }
            else
                $model->addError('passwordTextOld', 'Failed to load model');
        }

        return $this->render('profile', [
            'model' => $model
        ]);
    }

    /**
     *  Initialize admin user
     *
     * @return void
     */
    public function actionInit()
    {
        $user = User::findByUsername('secmon');
        if ($user === null)
        {
            $model = new User();
            $model->first_name = 'secmon';
            $model->last_name = 'admin';
            $model->username = 'secmon';
            $model->passwordText = Yii::$app->params['defaultPass'];
            $model->rolesList = [1, 100];
            $model->email = 'root@localhost';
            if ($model->save(false))
                return('done');
            else
                return('error');
        }
        else
            return('user allready exists');
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
