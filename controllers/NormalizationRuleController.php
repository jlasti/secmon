<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\filters\VerbFilter;
use app\models\NormalizationRule;
use app\services\NormalizationRuleService;

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
            $dataProvider = NormalizationRuleService::getAllRules();
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
     * Imports new rule file from URL using cURL.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (Yii::$app->user->isGuest) {
            // User not logged in
            return $this->goHome();
        } else {
            // User is logged in
            $model = new NormalizationRule();
            if ($model->load(Yii::$app->request->post())) {
                $ruleFileName = NormalizationRuleService::importNewRule($model); // znasilnenie modelu NormalizationRule na ulozenie URL
                return $this->redirect([
                    'view',
                    'ruleFileName' => $ruleFileName
                ]);
            }
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
                if (NormalizationRuleService::updateRule($model)) {
                    return $this->redirect([
                        'view',
                        'ruleFileName' => $model->ruleFileName
                    ]);
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
     * @param string $ruleFileName
     * @return mixed
     */
    public function actionDelete($ruleFileName)
    {
        if (Yii::$app->user->isGuest) {
            // User not logged in
            return $this->goHome();
        } else {
            // User logged in
            if (NormalizationRuleService::deleteRule($ruleFileName)) {
                return $this->redirect(['index']);
            }
        }
    }

    /**
     * Executes update of default rules.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $ruleFileName
     * @return mixed
     */
    public function actionRulesUpdate()
    {
        if (Yii::$app->user->isGuest) {
            // User not logged in
            return $this->goHome();
        } else {
            $activeRulesPath = NormalizationRuleService::deleteActiveRules();
            $pythonScriptPath = Yii::getAlias("@app/commands/rules_downloader.py");
            shell_exec("python3 $pythonScriptPath web");
            NormalizationRuleService::reactiveRules($activeRulesPath);
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
            $model = NormalizationRuleService::getRuleByFileName($ruleFileName);
            if (!is_null($model)) {
                return $model;
            } else {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }
    }
}