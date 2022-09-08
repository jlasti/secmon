<?php

namespace app\controllers;

use Yii;
use app\models\SecurityEvents;
use app\models\SecurityEventsSearch;
use app\models\SecurityEventsPage;
use app\models\Event\Analyzed;
use app\models\Event\AnalyzedConfig;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SecurityEventsController implements the CRUD actions for SecurityEvents model.
 */
class SecurityEventsController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all SecurityEvents models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $userId = Yii::$app->user->getId();
        $securityEventsPage = SecurityEventsPage::findOne(['user_id' => $userId]);

        // Security Events Page need to be created
        if (empty($securityEventsPage)) {
            $securityEventsPage = new SecurityEventsPage();
            $securityEventsPage->user_id = $userId;
            $securityEventsPage->refresh_time = '10S';
            $securityEventsPage->data_columns = 'id,datetime,type,application_protocol,source_address,destination_address,analyzed';
            return $securityEventsPage->save();
        }

        $searchModel = new SecurityEventsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->sort->defaultOrder = ['id' => SORT_DESC];

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SecurityEvents model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new SecurityEvents model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new SecurityEvents();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SecurityEvents model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing SecurityEvents model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    // perform analyse
    public function actionAnalyse(){
        $params = [':id' => $_GET['id'], ':norm' => $_GET['norm']];

        $event = new Analyzed();
        $event->Analyse($params);

        return $this->redirect(['/security-events-list/index']);
    }

    /**
     * Search for clusters which contains event_id
     * @param string $id
     * @return mixed
     */
    public function actionSearchclusters($id){
        return $this->redirect(['/events-clustered-filtered-clusters', 'event_id' => $id]);
    }

    // show map
    public function actionShow(){
        $params = [':id' => $_GET['id']];

        return $this->render('show');
    }

    // show heat map
    public function actionAll(){
        $params = [':id' => $_GET['id']];

        $analyzedConfig = new AnalyzedConfig();

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return json_encode($analyzedConfig->getAnalyzedCodeCount($params));
    }

    // show points map
    public function actionPoint(){
        $params = [':id' => $_GET['id']];

        $analyzedConfig = new AnalyzedConfig();

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return json_encode($analyzedConfig->getAnalyzedAllPoints($params));
    }

    /**
     * Finds the SecurityEvents model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return SecurityEvents the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $secId = preg_replace('/[^0-9]/','',$id);

        if (($model = SecurityEvents::findOne($secId)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
