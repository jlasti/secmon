<?php

namespace app\controllers;

use app\models\Event\AnalyzedConfig;
use Yii;
use app\models\EventsNormalized;
use app\models\EventsNormalizedSearch;
use app\models\Event\Analyzed;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EventsNormalizedController implements the CRUD actions for EventsNormalized model.
 */
class EventsNormalizedController extends Controller
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
     * Lists all EventsNormalized models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EventsNormalizedSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EventsNormalized model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new EventsNormalized model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EventsNormalized();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing EventsNormalized model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing EventsNormalized model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
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

        return $this->redirect(['/events-normalized-list/index']);
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
     * Finds the EventsNormalized model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return EventsNormalized the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $secId = preg_replace('/[^0-9]/','',$id);

        if (($model = EventsNormalized::findOne($secId)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
