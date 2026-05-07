<?php

namespace app\controllers;

use Yii;
use app\models\MispEvents;
use app\models\MispAttributes;
use app\models\MispSettings;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use app\commands\SynchronizerController as ConsoleSynchronizerController;

class SynchronizerController extends Controller
{
    /** Define access rules and HTTP verb filters. */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'toggle-sent' => ['POST'],
                ],
            ],
        ];
    }

    /** Show MISP events list with optional local filter. */
    public function actionIndex($local = null)
    {
        $query = MispEvents::find();
        if ($local) {
            $query->where(['event_uuid' => null]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['created_at' => SORT_DESC]],
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    /** Display events that are not yet exported. */
    public function actionPending()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => MispEvents::find()->where(['is_sent' => false]),
            'sort' => ['defaultOrder' => ['created_at' => SORT_DESC]],
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('pending', ['dataProvider' => $dataProvider]);
    }

    /** View a single MISP event and its attributes. */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $attributesProvider = new ActiveDataProvider([
            'query' => MispAttributes::find()->where(['event_id' => $id]),
            'sort' => ['defaultOrder' => ['timestamp' => SORT_DESC]],
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('view', [
            'model' => $model,
            'attributesProvider' => $attributesProvider,
        ]);
    }

    /** Permanently delete a MISP event record. */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Event deleted.');
        return $this->redirect(['index']);
    }

    /** Edit MISP connection settings and IP filters. */
    public function actionSettings()
    {
        $model = MispSettings::getSettings();

        if ($model->load(Yii::$app->request->post())) {
            $post = Yii::$app->request->post('MispSettings', []);

            $model->ip_filters = $post['ip_filters_json'] ?? '[]';

            $attributeTypes = $post['attribute_types'] ?? [];
            if (is_array($attributeTypes)) {
                $attributeTypes = array_filter($attributeTypes, 'strlen');
                $model->setAttributeTypesArray(array_values($attributeTypes));
            } else {
                $model->setAttributeTypesArray([]);
            }

            $model->setSyncIntervalMinutes($model->sync_interval);

            if (!empty($model->misp_url) xor !empty($model->misp_api_key)) {
                if (empty($model->misp_url)) {
                    $model->addError('misp_url', 'URL is required when API key is provided.');
                }
                if (empty($model->misp_api_key)) {
                    $model->addError('misp_api_key', 'API key is required when URL is provided.');
                }
            }

            if (!empty($model->misp_url) && !empty($model->misp_api_key)) {
                if (empty($model->organization_name)) {
                    $model->addError('organization_name', 'Organization name is required when MISP connection is configured.');
                }
            }

            if (!empty($model->misp_url) && !empty($model->misp_api_key) && !empty($model->organization_name) && !$model->hasErrors()) {
                $connectionTest = $model->testConnection();
                if (!$connectionTest['success']) {
                    $model->addError('misp_url', 'Connection test failed: ' . $connectionTest['message']);
                    $model->addError('misp_api_key', 'Connection test failed.');
                    Yii::$app->session->setFlash('error', 'Could not connect to MISP server. Check URL and API key.');
                    return $this->render('settings', [
                        'model' => $model,
                        'connectionStatus' => $connectionTest,
                    ]);
                }
            }

            if (!$model->hasErrors() && $model->save()) {
                Yii::$app->session->setFlash('success', 'Settings saved successfully.');
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('error', 'Please fix the errors below.');
            }
        }

        $connectionStatus = $model->testConnection();

        return $this->render('settings', [
            'model' => $model,
            'connectionStatus' => $connectionStatus,
        ]);
    }

    /** Test MISP API connectivity via AJAX. */
    public function actionTestConnection()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = MispSettings::getSettings();
        return $model->testConnection();
    }

    /** Trigger MISP attribute sync from daemon. */
    public function actionSyncNow()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        try {
            Yii::info('=== SYNC NOW triggered ===', 'misp');
            MispSettings::triggerSync();
            Yii::info('triggerSync() called successfully', 'misp');
            return ['success' => true, 'message' => 'Synchronization queued. Daemon will process it.'];
        } catch (\Exception $e) {
            Yii::error('=== SYNC NOW FAILED: ' . $e->getMessage(), 'misp');
            Yii::error('Stack trace: ' . $e->getTraceAsString(), 'misp');
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /** Trigger export of unsent events to MISP. */
    public function actionExportNow()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        try {
            Yii::info('=== EXPORT NOW triggered ===', 'misp');
            MispSettings::triggerExport();
            Yii::info('triggerExport() called successfully', 'misp');
            return ['success' => true, 'message' => 'Export queued. Daemon will process it.'];
        } catch (\Exception $e) {
            Yii::error('=== EXPORT NOW FAILED: ' . $e->getMessage(), 'misp');
            Yii::error('Stack trace: ' . $e->getTraceAsString(), 'misp');
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /** Find a MISP event by its primary key. */
    protected function findModel($id)
    {
        if (($model = MispEvents::findOne(['event_id' => $id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested MISP event does not exist.');
    }

    /** Toggle export flag for a local event. */
    public function actionToggleSent($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = $this->findModel($id);
        if ($model->event_uuid !== null) {
            return ['success' => false, 'message' => 'Only local events can be modified.'];
        }
        $model->is_sent = !$model->is_sent;
        $model->sent_at = $model->is_sent ? new \yii\db\Expression('NOW()') : null;
        if ($model->save(false)) {
            $status = $model->is_sent ? 'marked as do not export' : 'marked for export';
            return ['success' => true, 'message' => "Event $status."];
        }
        return ['success' => false, 'message' => 'Failed to update event.'];
    }
    
    public function actionSyncFullNow()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        try {
            Yii::info('=== FULL SYNC NOW triggered ===', 'misp');
            MispSettings::triggerFullSync();  // túto metódu ešte dopíšeme v modeli
            return ['success' => true, 'message' => 'Full sync queued. Daemon will process it.'];
        } catch (\Exception $e) {
            Yii::error('=== FULL SYNC NOW FAILED: ' . $e->getMessage(), 'misp');
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
}