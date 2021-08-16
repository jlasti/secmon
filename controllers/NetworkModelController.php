<?php

namespace app\controllers;

use app\models\Event\AnalyzedConfig;
use Yii;
use app\models\NetworkModel;
use app\models\Interfaces;
use app\models\NetworkModelSearch;
use app\controllers\InterfacesController;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * NetworkModelController implements the CRUD actions for NetworkModel model.
 */
class NetworkModelController extends Controller
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
     * Lists all NetworkModel models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new NetworkModelSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single NetworkModel model.
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
     * Creates a new NetworkModel model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id)
    {
        $model = new Interfaces();
        $model->network_model_id = $id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->network_model_id]);
        } else {

            return $this->render('//interfaces/create', [
                'model' => $model,
            ]);
        }
        
    }

    /**
     * Updates an existing NetworkModel model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing NetworkModel model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $interfaces = Interfaces::getInterfacesByNetworkModel($id)->getModels();
        foreach($interfaces as $interface){
            $interface->delete();
        }

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionMerge($id){

        return $this->render('_merge', [
            'id' => $id,
        ]);
    }

    public function actionProcessselected($id, $del_id){
        $del_model = $this->findModel($del_id);
        $interfaces = Interfaces::getInterfacesByNetworkModel($del_id)->getModels();

        foreach($interfaces as $interface){
            $interface->network_model_id = $id;
            $interface->save();
        }

        $new_interface = new Interfaces();
        $new_interface->network_model_id = $id;
        $new_interface->ip_address = $del_model->ip_address;
        $new_interface->mac_address = $del_model->mac_address;
        $new_interface->name = $del_model->description;

        if($new_interface->save()){
            $del_model->delete();
            return $this->redirect(['view', 'id' => $id]);
        }
        return $this->redirect(['index']);
     }

    /**
     * Finds the NetworkModel model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return NetworkModel the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function findModel($id)
    {
        if (($model = NetworkModel::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}