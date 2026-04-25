<?php

namespace app\controllers\api;

use Yii;
use app\models\Dashboard;
use app\models\Dashboard\DashboardWidget;
use app\models\SecurityEvents;
use app\models\Filter;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\filters\ContentNegotiator;
use yii\filters\auth\HttpBearerAuth;
use yii\web\Response;
use yii\rest\Controller;
use yii\helpers\Json;
use app\services\ChartDataService;


class DashboardWidgetController extends Controller
{

    private $chartDataService;

    public function __construct($id, $module, ChartDataService $chartDataService, $config = [])
    {
        $this->chartDataService = $chartDataService;
        parent::__construct($id, $module, $config);
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];

        $behaviors['verbFilter'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'delete' => ['POST'],
            ],
        ];

        return $behaviors;
    }

    /**
     * Helper to check if the current user is authenticated.
     * @throws ForbiddenHttpException if the user is a guest.
     */
    protected function checkAccess()
    {
        if (Yii::$app->user->isGuest) {
            throw new ForbiddenHttpException('You must be logged in to access this resource.');
        }
    }

    /**
     * Get dashboard widget with filter applied content
     * @param integer $widgetId
     * @param integer $pagination
     * @param integer|string|null $lastId
     * @return array
     */
    public function actionContent($widgetId, $pagination = 1, $lastId = null)
    {
        $this->checkAccess();
        
        $widget = $this->findModel($widgetId);
        $this->checkWidgetOwnership($widget);

        $filter = !empty($widget->filter_id) ? Filter::findOne(['id' => $widget->filter_id]) : null;

        if (!empty($filter) && $filter->user_id != Yii::$app->user->getId()) {
            throw new ForbiddenHttpException('You do not have permission to access this filter.');
        }

        $chartType = $widget->chart_type;
        $timeframe = $widget->timeframe ?? "";
        $config = is_string($widget->config) ? Json::decode($widget->config) : $widget->config;
        switch ($chartType) {
            case "pieChart":
                // Parse config to get the field to chart
                $field = $config['pie_chart_variable'] ?? 'cef_severity';
                
                return [
                    'chartType' => $chartType,
                    'timeframe' => $timeframe,
                    'lastId' => $this->chartDataService->getLatestFilteredEventId($widget->filter_id, $timeframe),
                    'data' => $this->chartDataService->getFilteredEventsPieChart($widget->filter_id, $field, $timeframe, $lastId)
                ];
                
            case "barChart":
                // Parse config to get the field to chart
                $field = $config['bar_chart_variable'] ?? 'cef_severity';
                
                return [
                    'chartType' => $chartType,
                    'timeframe' => $timeframe,
                    'lastId' => $this->chartDataService->getLatestFilteredEventId($widget->filter_id, $timeframe),
                    'data' => $this->chartDataService->getFilteredEventsBarChart($widget->filter_id, $field, $timeframe, $lastId)
                ];
                
            case "lineChart":
                return [
                    'chartType' => $chartType,
                    'timeframe' => $timeframe,
                    'lastId' => $this->chartDataService->getLatestFilteredEventId($widget->filter_id, $timeframe),
                    'data' => $this->chartDataService->getFilteredEventsLineChart($widget->filter_id, $timeframe, $lastId)
                ];
                
            case "table":
                $config = is_string($widget->config) ? Json::decode($widget->config) : $widget->config;
                $columns = $config['table_columns'] ?? ['id', 'datetime', 'device_host_name', 'application_protocol'];
                $timeframe = $widget->timeframe ?? '';
                
                $filteredData = $this->chartDataService->getFilteredEventsTableWidget($widget->filter_id, $pagination, $columns, $timeframe, $lastId);
                $count = $this->chartDataService->getFilteredEventsCountForTableWidget($widget->filter_id, $timeframe, $lastId);

                return [
                    'chartType' => $chartType,
                    'timeframe' => $timeframe,
                    'lastId' => $this->chartDataService->getLatestFilteredEventId($widget->filter_id, $timeframe),
                    'pagination' => [
                        'page' => $pagination,
                        'total' => $count,
                    ],
                    'columns' => $columns,
                    'data' => $filteredData,
                ];
                
            case "geoMap":
                $geoConfig = is_string($widget->config) ? Json::decode($widget->config) : $widget->config;
                $locationType = $geoConfig['location_type'] ?? 'source';
                return [
                    'chartType' => $chartType,
                    'timeframe' => $timeframe,
                    'lastId' => $this->chartDataService->getLatestFilteredEventId($widget->filter_id, $timeframe),
                    'data' => $this->chartDataService->getFilteredEventsGeoMap($widget->filter_id, $locationType, $timeframe, $lastId)
                ];
                
            default:
                throw new \yii\web\BadRequestHttpException('Invalid chart type.');
        }
    }

    public function actionAllSecurityEventFields()
    {
        return [
            'fields' => array_keys(\app\models\SecurityEvents::columns())
        ];
    }

    /**
     * Update dashboard widget filter and configuration
     * @param integer $widgetId
     * @return array
     */
    public function actionUpdateSettings()
    {
        $widgetId = Yii::$app->request->post('widget_id');

        $this->checkAccess();
        $widget = $this->findModel($widgetId);
        $this->checkWidgetOwnership($widget);


        $title = Yii::$app->request->post('title');
        $chartType = Yii::$app->request->post('chart_type');
        $dashboardId = Yii::$app->request->post('dashboard_id');
        $filterId = Yii::$app->request->post('filter_id');
        $timeframe = Yii::$app->request->post('timeframe');
        $config = Yii::$app->request->post('config');

        if (!empty($filterId)) {
            $filter = Filter::findOne(['id' => $filterId]);
            if (empty($filter) || $filter->user_id != Yii::$app->user->getId()) {
                throw new ForbiddenHttpException('You do not have permission to use this filter.');
            }
            $widget->filter_id = $filterId;
        } else{
            $widget->filter_id = null; // Clear filter if not provided
        }
        
        if (!empty($title)) {
            $widget->title = $title;
        }

        if (!empty($chartType)) {
            $widget->chart_type = $chartType;
        }

        if (!empty($dashboardId)) {
            $dashboard = Dashboard::findOne(['id' => $dashboardId]);
            if (empty($dashboard) || $dashboard->user_id != Yii::$app->user->getId()) {
                throw new ForbiddenHttpException('You do not have permission to use this dashboard.');
            }
            $widget->dashboard_id = $dashboardId;
        }

        if (!empty($timeframe)) {
            $widget->timeframe = $timeframe;
        }

        if (!empty($config)) {
            // Validate config based on chart type
            $configArray = is_string($config) ? Json::decode($config) : $config;
            $dbCols = array_keys(SecurityEvents::columns()); 
            if ($chartType == 'table') {
                if (!empty($configArray['table_columns'])) {
                    $validCols = [];
                    foreach ($configArray['table_columns'] as $col) {
                        if (in_array($col, $dbCols)) {
                            $validCols[] = $col;
                        }
                    }
                    $configArray['table_columns'] = $validCols;
                    $config = $configArray;
                }
            }
            elseif($chartType == 'pieChart') {
                if (!empty($configArray['pie_chart_variable']) && in_array($configArray['pie_chart_variable'], $dbCols)) {
                    $config = $configArray;
                } else {
                    throw new \yii\web\BadRequestHttpException('Invalid variable for pie chart configuration.');
                }
            }
            elseif($chartType == 'barChart') {
                if (!empty($configArray['bar_chart_variable']) && in_array($configArray['bar_chart_variable'], $dbCols)) {
                    $config = $configArray;
                } else {
                    throw new \yii\web\BadRequestHttpException('Invalid variable for bar chart configuration.');
                }
            }

            $widget->config = is_array($config) ? Json::encode($config) : $config;
        }

        if ($widget->save()) {
            return [
                'success' => true,
                'widget' => $widget,
            ];
        }

        return [
            'success' => false,
            'errors' => $widget->errors,
        ];
    }

    /**
     * Determine appropriate time unit for aggregation
     * @param Filter $timeFilter
     * @return string
     */
    protected function determineTimeUnit($timeFilter)
    {
        // Implementation would depend on your time filter structure
        // For now, return a sensible default
        return 'hour';
    }

    /**
     * Check if the current user owns the dashboard that contains this widget
     * @param DashboardWidget $widget
     * @throws ForbiddenHttpException
     */
    protected function checkWidgetOwnership($widget)
    {
        $dashboard = Dashboard::findOne(['id' => $widget->dashboard_id]);
        
        if (empty($dashboard) || $dashboard->user_id != Yii::$app->user->getId()) {
            throw new ForbiddenHttpException('You do not have permission to access this widget.');
        }
    }

    /**
     * Finds the DashboardWidget model based on its primary key value.
     * @param integer $id
     * @return DashboardWidget the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DashboardWidget::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested dashboard widget does not exist.');
    }
}