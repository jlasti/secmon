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
        if (Yii::$app->user->isGuest) {
            // User not logged in
            return $this->goHome();
        } else {
            // User logged in
            $dataProvider = NormalizationRuleSearch::getAllRules();
            return $this->render('index', [
                'dataProvider' => $dataProvider,
            ]);
        }
    }

    /**
     * Displays a single NormalizationRule model.
     * @param string $ruleFileName
     * @return mixed
     */
    public function actionView($ruleFileName)
    {
        if (Yii::$app->user->isGuest) {
            // User not logged in
            return $this->goHome();
        } else {
            // User logged in
            $model = $this->findModel($ruleFileName);
            return $this->render('view', [
                'model' => $model,
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
        // TODO: rework
        if (Yii::$app->user->isGuest) {
            // User not logged in
            return $this->goHome();
        } else {
            // User is logged in
            $model = new NormalizationRule();
            if ($model->load(Yii::$app->request->post())) {
                // Handle creation of UI file
                /* return $this->redirect(['view', 'id' => $model->id]); */
            }
            $model->active = true;
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing NormalizationRule.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $ruleFileName
     * @return mixed
     */
    public function actionUpdate($ruleFileName)
    {
        if (Yii::$app->user->isGuest) {
            // User not logged in
            return $this->goHome();
        } else {
            // User logged in
            $model = $this->findModel($ruleFileName);
            if (!$model) {
                // NormalizationRule model not found.
                return $this->redirect(['index']);
            }
            if (Yii::$app->request->post()) {
                // POST
                $model->load(Yii::$app->request->post());
                if (NormalizationRuleSearch::updateRule($model)) {
                    return $this->redirect(['view', 'ruleFileName' => $model->ruleFileName]);
                }
            } else {
                // GET
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
     * Finds the NormalizationRule model based on unique name.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $ruleFileName
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($ruleFileName)
    {
        if (Yii::$app->user->isGuest) {
            // User not logged in
            return $this->goHome();
        } else {
            // User logged in
            $model = NormalizationRuleSearch::getRuleByFileName($ruleFileName);
            if (!is_null($model)) {
                return $model;
            } else {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }
    }
}