<?php

namespace app\controllers;

use Yii;
use app\models\View;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;

/**
 * ViewController implements the CRUD actions for View model.
 */
class ViewController extends Controller
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
     * Lists all View models.
     * @return mixed
     */
    public function actionIndex()
    {
        $userId = Yii::$app->user->getId();

        $views = View::findAll(['user_id' => $userId]);

        // No view was created, create default
        if (count($views) == 0) {
            $view = new View();
            $view->name = 'Default';
            $view->user_id = $userId;
            $view->active = true;
            $view->save();
            array_push($views,$view);
        }

        $activeViewId = array_filter($views, function($temp) {
                            return $temp['active'] == 1;
                        })[0]->getAttribute('id');

        $components = View\Component::findAll(['view_id' => $activeViewId]);

        return $this->render('index', [
            'views' => $views,
            'activeViewId' => $activeViewId,
            'components' => Json::encode($components)
        ]);
    }

    /**
     * Displays a single View model.
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
     * Creates a new View model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new View();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing View model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing View model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the View model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return View the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = View::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionChangeView($viewId, $activeViewId)
    {
        $components = $this->getComponentsOfView($viewId);

        $this->changeActiveAttributeOfView($viewId, 1);
        $this->changeActiveAttributeOfView($activeViewId, 0);

        return Json::encode($components);
    }

    protected function getComponentsOfView($viewId)
    {
        $components = View\Component::findAll(['view_id' => $viewId]);

        return $components;
    }

    protected function changeActiveAttributeOfView($viewId, $active)
    {
        $view = View::findOne($viewId);
        $view->active = $active;
        $view->update();

        return $viewId;
    }

    public function actionCreateComponent()
    {
        $params = Yii::$app->getRequest()->getQueryParams();
        $viewId = $params['viewId'];
        $config = $params['config'];

        $component = new View\Component();
        $component->view_id = $viewId;
        $component->config = $config;

        return $component->save() ? $component->id : false;
    }

    public function actionUpdateComponent()
    {
        $params = Yii::$app->getRequest()->getQueryParams();
        $componentId = $params['componentId'];
        $config = $params['config'];

        $component = View\Component::findOne($componentId);
        $component->config = $config;

        return !empty($component) ? ($component->update() ? $component->id : false) : false;
    }

    public function actionDeleteComponent()
    {
        $params = Yii::$app->getRequest()->getQueryParams();
        $componentId = $params['componentId'];

        $component = View\Component::findOne($componentId);

        return !empty($component) ? ($component->delete() ? true : false) : false;
    }
}
