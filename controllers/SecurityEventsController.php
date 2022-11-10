<?php

namespace app\controllers;

use Yii;
use app\models\SecurityEvents;
use app\models\SecurityEventsSearch;
use app\models\SecurityEventsPage;
use app\models\Event\Analyzed;
use app\models\Event\AnalyzedConfig;
use app\models\Filter;
use app\models\FilterRule;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

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
                'access' => [
                    'class'=> AccessControl::className(),
                    'only' =>  ['index','view'],
                    'rules' => [
                        [
                            'actions' => ['index', 'view'],
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
            $securityEventsPage->save();
        }

        $filterId = $securityEventsPage->filter_id;
        $timeFilterId = $securityEventsPage->time_filter_id;

        $searchModel = new SecurityEventsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams, $filterId, $timeFilterId);
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

    public function actionUpdateRefreshTime()
    {
        $userId = Yii::$app->user->getId();
        $model = new SecurityEventsPage();

        if($model->load(Yii::$app->request->post()) && $model->refresh_time && $model->validate())
        {
            $securityEventsPage = SecurityEventsPage::findOne(['user_id' => $userId]);
            if($this->checkMinRefreshTimeValue($model->refresh_time))
                $securityEventsPage->refresh_time = $model->refresh_time;
            else
                $securityEventsPage->refresh_time = '10S';
            if(!empty($securityEventsPage))
                $securityEventsPage->save();
        }

        return $this->redirect(['index']);
    }

    public function actionApplySelectedFilter()
    {
        $userId = Yii::$app->user->getId();
        $model = new Filter();

        if($model->load(Yii::$app->request->post()) && $model->validate())
        {
            if(Filter::findOne(['user_id' => $userId, 'name' => $model->name]))
                $filterId = Filter::findOne(['user_id' => $userId, 'name' => $model->name])->getAttribute('id');
            else
                return $this->redirect(['index']);
                
            $securityEventsPage = SecurityEventsPage::findOne(['user_id' => $userId]);
            if(!empty($securityEventsPage) && $filterId){
                $securityEventsPage->filter_id = $filterId;
                $securityEventsPage->update();
            }
        }
        else
        {
            $securityEventsPage = SecurityEventsPage::findOne(['user_id' => $userId]);
            if(!empty($securityEventsPage)){
                $securityEventsPage->filter_id = null;
                $securityEventsPage->update();
            }
        }

        return $this->redirect(['index']);
    }

    public function actionRemoveSelectedFilter()
    {
        $userId = Yii::$app->user->getId();
        $securityEventsPage = SecurityEventsPage::findOne(['user_id' => $userId]);
        if(!empty($securityEventsPage)){
            $securityEventsPage->filter_id = null;
            $securityEventsPage->update();
        }

        return $this->redirect(['index']);
    }

    public function actionDeleteSelectedFilter()
    {
        $userId = Yii::$app->user->getId();
        $securityEventsPage = SecurityEventsPage::findOne(['user_id' => $userId]);
        if(!empty($securityEventsPage->filter_id)){
            $filter = Filter::findOne(['id' => $securityEventsPage->filter_id]);
            $filter->delete();
        }

        return $this->redirect(['index']);
    }

    public function actionGetRefreshTime()
    {
        $userId = Yii::$app->user->getId();
        $securityEventsPage = SecurityEventsPage::findOne(['user_id' => $userId]);
        
        if($securityEventsPage->refresh_time != null && $securityEventsPage->refresh_time != "")
            return Json::encode($securityEventsPage->refresh_time);
        else
            return Json::encode('10S');
    }

    public function actionUpdateSelectedColumns()
    {
        $userId = Yii::$app->user->getId();
        $securityEventsPage = SecurityEventsPage::findOne(['user_id' => $userId]);

        if (Yii::$app->request->post()) {
            
            $columns = Yii::$app->request->post('value');
            $selectedColumns = "";

            foreach ($columns as $index => $column)
            {
                if($index)
                    $selectedColumns .= ',' . $column ;
                else
                    $selectedColumns .= $column ;
            }
                
            $securityEventsPage->data_columns = $selectedColumns;
            if(!empty($securityEventsPage))
                $securityEventsPage->update();

            return $this->redirect(['index']);
        }
        else
        {
            $securityEventsPage->data_columns = 'id,datetime,type,application_protocol,source_address,destination_address,analyzed';
            if(!empty($securityEventsPage))
                $securityEventsPage->update();

            return $this->redirect(['index']);
        }
    }

    public function actionUpdateTimeFilter()
    {
        $userId = Yii::$app->user->getId();
        $securityEventsPage = SecurityEventsPage::findOne(['user_id' => $userId]);

        if(Yii::$app->request->post() && $securityEventsPage) {
            $timeFilterType = Yii::$app->request->post('timeFilterType');
            $absoluteTimeFrom = Yii::$app->request->post('absoluteTimeFrom');
            $absoluteTimeTo = Yii::$app->request->post('absoluteTimeTo');
            $relativeTime = Yii::$app->request->post('relativeTime');

            if($timeFilterType == 'relative' && (empty($relativeTime) || !preg_match('/^\d{1,5}[YMWDHmS]{1}$/', $relativeTime)))
                return $this->redirect(['index']);

            if(!empty($securityEventsPage->time_filter_id))
            {
                $timeFilter = Filter::findOne(['id' => $securityEventsPage->time_filter_id])->delete();
            }

            if($timeFilterType == 'absolute')
            {
                if(empty($absoluteTimeFrom) && empty($absoluteTimeTo))
                {
                    $securityEventsPage->time_filter_type = 'absolute';
                    $securityEventsPage->time_filter_id = null;
                    $securityEventsPage->update();
                    return $this->redirect(['index']);
                }

                // Create new Time Filter
                $timeFilter = new Filter();
                $timeFilter->user_id = $userId;
                $timeFilter->name = 'AbsoluteTimeFilter_' . $userId;
                $timeFilter->time_filter = true;
                $timeFilter->insert();
                
                $position = 0;

                // Create Filter Rule for Time From
                if($absoluteTimeFrom)
                {
                    $timeFilterRule = new FilterRule();
                    $timeFilterRule->filter_id = $timeFilter->id;
                    $timeFilterRule->type = 'date';
                    $timeFilterRule->value = $absoluteTimeFrom;
                    $timeFilterRule->operator = '>=';
                    $timeFilterRule->position = $position++;
                    $timeFilterRule->column = 'datetime';
                    $timeFilterRule->save();
                }

                // Create Filter Rule for Time To
                if($absoluteTimeTo)
                {
                    $timeFilterRule = new FilterRule();
                    $timeFilterRule->filter_id = $timeFilter->id;
                    $timeFilterRule->type = 'date';
                    $timeFilterRule->value = $absoluteTimeTo;
                    $timeFilterRule->operator = '<=';
                    if($absoluteTimeFrom)
                        $timeFilterRule->logic_operator = 'AND';
                    $timeFilterRule->position = $position;
                    $timeFilterRule->column = 'datetime';
                    $timeFilterRule->save();
                }
                
                // Update Security Events Page Settings
                $securityEventsPage->time_filter_type = 'absolute';
                $securityEventsPage->time_filter_id = $timeFilter->id;
                $securityEventsPage->update();
            }
            
            if($timeFilterType == 'relative')
            {
                // Create new Time Filter
                $timeFilter = new Filter();
                $timeFilter->user_id = $userId;
                $timeFilter->name = 'RelativeTimeFilter_' . $userId;
                $timeFilter->time_filter = true;
                $timeFilter->insert();

                // Create Relative Filter Rule
                $timeFilterRule = new FilterRule();
                $timeFilterRule->filter_id = $timeFilter->id;
                $timeFilterRule->type = 'date';
                $timeFilterRule->value = $relativeTime;
                $timeFilterRule->operator = 'Last';
                $timeFilterRule->column = 'datetime';
                $timeFilterRule->save();

                // Update Security Events Page Settings
                $securityEventsPage->time_filter_type = 'relative';
                $securityEventsPage->time_filter_id = $timeFilter->id;
                $securityEventsPage->update();

            }
        }
        return $this->redirect(['index']);
    }

    public function actionAddAttributeToFilter()
    {
        $userId = Yii::$app->user->getId();
        $securityEventsPage = SecurityEventsPage::findOne(['user_id' => $userId]);
        
        if(Yii::$app->request->post() && $securityEventsPage) {
            $logicOperator = Yii::$app->request->post('operator');
            $negation = Yii::$app->request->post('negation');
            $value = Yii::$app->request->post('value');
            $column = Yii::$app->request->post('column');

            // If one of 
            if(empty($logicOperator) || empty($negation) || empty($value)|| empty($column) || $value == '(not set)' )
                return $this->redirect(['index']);

            // If none Event Filter is Applied, therefore new Filter need to be created
            if(empty($securityEventsPage->filter_id))
            {
                // Create new Event Filter
                $eventFilter = new Filter();
                $eventFilter->user_id = $userId;
                $eventFilter->name = $column . '_' . time();
                $eventFilter->insert();

                // Create Event Filter Rule
                $eventFilterRule = new FilterRule();
                $eventFilterRule->filter_id = $eventFilter->id;
                
                if($column == 'datetime' || $column == 'start_time' || $column == 'end_time' || $column == 'file_create_time' || $column == 'file_modification_time'
                    || $column == 'old_file_create_time' || $column == 'old_file_modification_time' || $column == 'device_receipt_time'){
                    $eventFilterRule->type = 'date';
                    $value = date("Y-m-d h:i", strtotime($value));
                }
                else
                    $eventFilterRule->type = 'compare';
                
                if($negation == 'true')
                    $eventFilterRule->operator = '!=';
                else
                    $eventFilterRule->operator = '=';

                $eventFilterRule->value = $value;
                $eventFilterRule->position = 0;
                $eventFilterRule->column = $column;
                $eventFilterRule->save();

                // Update Security Events Page Settings
                $securityEventsPage->filter_id = $eventFilter->id;
                $securityEventsPage->update();
            }
            else
            {
                $eventFilterRules = FilterRule::findAll(['filter_id' => $securityEventsPage->filter_id]);

                // Create Event Filter Rule
                $eventFilterRule = new FilterRule();
                $eventFilterRule->filter_id = $securityEventsPage->filter_id;
                
                if($column == 'datetime' || $column == 'start_time' || $column == 'end_time' || $column == 'file_create_time' || $column == 'file_modification_time'
                    || $column == 'old_file_create_time' || $column == 'old_file_modification_time' || $column == 'device_receipt_time'){
                    $eventFilterRule->type = 'date';
                    $value = date("Y-m-d h:i", strtotime($value));
                }
                else
                    $eventFilterRule->type = 'compare';
                
                if($negation == 'true')
                    $eventFilterRule->operator = '!=';
                else
                    $eventFilterRule->operator = '=';

                $eventFilterRule->logic_operator = $logicOperator;
                $eventFilterRule->value = $value;
                $eventFilterRule->position = count($eventFilterRules);
                $eventFilterRule->column = $column;
                $eventFilterRule->save();
            }
        }
        return $this->redirect(['index']);
    }

    public function checkMinRefreshTimeValue($refreshTime)
    {
        $timeUnit = substr($refreshTime, strlen($refreshTime)-1, strlen($refreshTime));
        $refreshTime = (int)(substr($refreshTime, 0, strlen($refreshTime)-1));

        if ($timeUnit == "S" && $refreshTime < 10)
            return false;
        return true;
    }

    public static function replaceColumns($rawDataColumns, $searchModel)
    {
        $replaceDataColumns = [];
        array_push($replaceDataColumns, ['class' => 'yii\grid\SerialColumn',]);

        foreach($rawDataColumns as $column)
        {
            switch ($column) {
                case 'cef_severity':
                    array_push($replaceDataColumns, [
                        'attribute' => 'cef_severity',
                        'value' => 'cef_severity',
                        'contentOptions' => function ($dataProvider, $key, $index, $column) {
                            $array = [
                                ['id' => '1', 'data' => '#00DBFF'],
                                ['id' => '2', 'data' => '#00DBFF'],
                                ['id' => '3', 'data' => '#00FF00'],
                                ['id' => '4', 'data' => '#00FF00'],
                                ['id' => '5', 'data' => '#FFFF00'],
                                ['id' => '6', 'data' => '#FFFF00'],
                                ['id' => '7', 'data' => '#CC5500'],
                                ['id' => '8', 'data' => '#CC5500'],
                                ['id' => '9', 'data' => '#FF0000'],
                                ['id' => '10', 'data' => '#FF0000'],
                            ];
                            if (0 < $dataProvider->cef_severity && $dataProvider->cef_severity < 11){
                                $map = ArrayHelper::map($array, 'id', 'data');
                                return ['style' => 'background-color:'.$map[$dataProvider->cef_severity]];
                            } else {
                                return ['style' => 'background-color:#FFFFFF'];
                            }
                        }
                    ]);
                    break;
                case 'datetime':
                    array_push($replaceDataColumns, [
                        'attribute' => 'datetime',
                        'value' => 'datetime',
                        'format' => 'raw',
                        'filter' => \macgyer\yii2materializecss\widgets\form\DatePicker::widget([
                            'model' => $searchModel,
                            'attribute' => 'datetime',
                            'clientOptions' => [
                                'format' => 'yyyy-mm-dd'
                            ]
                        ])
                    ]);
                    break;
                case 'analyzed':
                    array_push($replaceDataColumns, [
                        'class' => '\dosamigos\grid\columns\BooleanColumn',
                        'attribute' => 'analyzed',
                        'treatEmptyAsFalse' => true
                    ]);
                    break;
                default:
                    array_push($replaceDataColumns, $column);
            }
        }

        array_push($replaceDataColumns, ['class' => 'yii\grid\ActionColumn', 'template'=>'{view}']);

        return $replaceDataColumns;
    }
}
