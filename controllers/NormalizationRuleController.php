<?php


namespace app\controllers;

use app\models\NormalizationRule\NormalizationRuleSearch;
use Yii;
use app\models\NormalizationRule;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * NormalizationRuleController implements the CRUD actions for NormalizationRule model.
 */

class NormalizationRuleController extends Controller
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
        ];
    }
    /**
     * Lists all NormalizationRule models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) { // user not logged in
            return $this->goHome();
        } else { // user logged in
            $searchModel = new NormalizationRuleSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
    }
    /**
     * Displays a single NormalizationRule model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (Yii::$app->user->isGuest) { // user not logged in
            return $this->goHome();
        } else { // user logged in
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }
    }
    /**
     * Creates a new NormalizationRule model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (Yii::$app->user->isGuest) { // user not logged in
            return $this->goHome();
        } else { // user logged in
            $model = new NormalizationRule();

            if($model->load(Yii::$app->request->post()))
            {
                $model->normalizationRuleFile = UploadedFile::getInstance($model, 'normalizationRuleFile');
                if($model->upload()){

                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
            $model->state = true;
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }
    /**
     * Updates an existing NormalizationRule model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (Yii::$app->user->isGuest) { // user not logged in
            return $this->goHome();
        } else { // user logged in
            $model = $this->findModel($id);
            if ($model->load(Yii::$app->request->post())) {

                $model->changeRepository();

                if($model->save())

                    return $this->redirect(['view', 'id' => $model->id]);

            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
    }
    /**
     * Deletes an existing NormalizationRule model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (Yii::$app->user->isGuest) { // user not logged in
            return $this->goHome();
        } else { // user logged in
            $model = $this->findModel($id);
            if (file_exists($model->link)) {
                unlink($model->link);
                $model->delete();
                exec("sudo systemctl restart secmon-normalizer.service");
            }
            return $this->redirect(['index']);
        }
    }
    /**
     * Finds the NormalizationRule model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return NormalizationRule the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (Yii::$app->user->isGuest) { // user not logged in
            return $this->goHome();
        } else { // user logged in
            if (($model = NormalizationRule::findOne($id)) !== null) {
                return $model;
            } else {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }
    }
}