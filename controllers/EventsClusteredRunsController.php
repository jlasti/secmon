<?php

namespace app\controllers;

use app\models\EventsClusteredRuns;
use app\models\EventsClusteredRunsSearch;
use Yii;
use yii\db\StaleObjectException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * EventsClusteredRunsController implements the CRUD actions for EventsClusteredRuns model.
 */
class EventsClusteredRunsController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class'=> AccessControl::className(),
                'only' =>  ['index','view','create','update','delete','minisom','kmedian'],
                'rules' => [
                    [
                        'actions' => ['index','view','create','update','delete','minisom','kmedian'],
                        'allow' => true,
                        'roles' => ['@']
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all EventsClusteredRuns models.
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionIndex()
    {
        $searchModel = new EventsClusteredRunsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EventsClusteredRuns model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $secId = preg_replace('/[^0-9]/','',$id);

        return $this->redirect(['/events-clustered-clusters', 'run_id' => $secId]);
    }

    /**
     * Creates a new EventsClusteredRuns model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EventsClusteredClusters();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing EventsClusteredRuns model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException
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
     * Deletes an existing EventsClusteredRuns model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        try {
            $this->findModel($id)->delete();
        } catch (StaleObjectException $e) {
        } catch (NotFoundHttpException $e) {
        } catch (\Throwable $e) {
        }

        return $this->redirect(['index']);
    }

    /**
     * Run minisom algorithm
     * @return mixed
     */
    public function actionMinisom()
    {
        $command = escapeshellcmd('/usr/bin/python3.9 /var/www/html/secmon/commands/miniSOM.py');
        shell_exec($command);

        return $this->redirect(['/events-clustered-runs']);
    }

    /**
     * Run k-median algorithm
     * @return mixed
     */
    public function actionKmedian()
    {
        $command = escapeshellcmd('/usr/bin/python3.9 /var/www/html/secmon/commands/anomaly_script.py');
        shell_exec($command);

        return $this->redirect(['/events-clustered-runs']);
    }

    /**
     * Finds the EventsClusteredRuns model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return EventsClustered the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EventsClusteredRuns::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
