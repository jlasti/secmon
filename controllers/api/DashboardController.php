<?php

namespace app\controllers\api;

use Yii;
use app\models\Dashboard;
use app\models\Dashboard\DashboardWidget;
use app\models\Dashboard\WidgetLayout;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;
use yii\filters\ContentNegotiator;
use yii\filters\auth\HttpBearerAuth;
use yii\web\Response;
use yii\rest\Controller;
use app\models\Filter;


class DashboardController extends Controller
{
    public $enableCsrfValidation = true;

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
                'dashboards' => ['GET'],
                'view' => ['GET'],
                'create' => ['POST'],
                'update' => ['PUT', 'PATCH'],
                'delete' => ['DELETE'],
                'change-active' => ['POST'],
                'create-widget' => ['POST'],
                'delete-widget' => ['DELETE'],
                'update-widget-layout' => ['POST', 'PUT'],
                'get-refresh-times' => ['GET'],
                'filters' => ['GET'],
            ],
        ];

        return $behaviors;
    }

    protected function checkAccess()
    {
        if (Yii::$app->user->isGuest) {
            throw new ForbiddenHttpException('You must be logged in to access this resource.');
        }
    }

    public function actionDashboards()
    {
        $this->checkAccess();

        $userId = Yii::$app->user->getId();
        $dashboards = Dashboard::findAll(['user_id' => $userId]);

        // If no dashboard exists, create a default one
        if (empty($dashboards)) {
            $dashboard = new Dashboard();
            $dashboard->name = 'Default Dashboard';
            $dashboard->user_id = $userId;
            $dashboard->active = 1;
            $dashboard->refresh_time = '5S';
            if ($dashboard->save()) {
                $dashboards[] = $dashboard;
            } else {
                Yii::error('Failed to create default dashboard: ' . print_r($dashboard->errors, true));
            }
        }
            $safeDashboards = array_map(function (Dashboard $dashboard) {
            $dashboardArray = $dashboard->toArray();
            unset($dashboardArray['user_id']);
            return $dashboardArray;
        }, $dashboards);

        return $safeDashboards;
    }

    public function actionDashboard($id)
    {
        $this->checkAccess();
        return $this->findModel($id);
    }

    public function actionCreate()
    {
        $this->checkAccess();

        $model = new Dashboard();
        $model->user_id = Yii::$app->user->getId();

        // Deactivate all current active dashboards for the user
        Dashboard::updateAll(['active' => 0], ['user_id' => $model->user_id, 'active' => 1]);

        $model->load(Yii::$app->request->post(), '');
        $model->active = 1;

        if ($model->save()) {
            Yii::$app->response->statusCode = 201;
            return $model;
        }

        Yii::$app->response->statusCode = 422;
        return $model->errors;
    }

    public function actionUpdate($id)
    {
        $this->checkAccess();

        $model = $this->findModel($id);

        $model->active = 1;
        $model->load(Yii::$app->request->post(), '');

        if ($model->save()) {
            return $model;
        }

        Yii::$app->response->statusCode = 422;
        return $model->errors;
    }

    public function actionDelete($id)
    {
        $this->checkAccess();
        $model = $this->findModel($id); // This checks ownership

        if ($model->delete()) {
            Yii::$app->response->statusCode = 204;
            return true;
        }

        Yii::$app->response->statusCode = 500;
        return false;
    }


    public function actionChangeActive()
    {
        $newDashboardId = Yii::$app->request->post('newDashboardId');

        if (!$newDashboardId) {
            throw new \yii\web\BadRequestHttpException('Missing newDashboardId parameter in POST request.');
        }
        $this->checkAccess();
        $userId = Yii::$app->user->getId();
        Dashboard::updateAll(['active' => 0], ['user_id' => $userId]);
        $dashboard = $this->findModel($newDashboardId); 

        $dashboard->active = 1;
        $dashboard->save(false); 

        return $this->getWidgetsOfDashboard($newDashboardId);
    }

    public function actionCreateWidget()
    {
        $this->checkAccess();

        $request = Yii::$app->request;
        $title = $request->post('title');
        $dashboardId = $request->post('dashboard_id');
        $config = $request->post('config');
        $chartType = $request->post('chart_type');

        $this->findModel($dashboardId);

        $widget = new DashboardWidget();
        $widget->dashboard_id = $dashboardId;
        $widget->title = $title;
        $widget->chart_type = $chartType;
        $widget->config = $config;

        if ($widget->save()) {
            Yii::$app->response->statusCode = 201;
            return [
                'widget' => $widget,
                'id' => $widget->id,
            ];
        }

        Yii::$app->response->statusCode = 422;
        return $widget->errors;
    }

    public function actionDeleteWidget($widgetId)
    {
        $this->checkAccess();

        $widget = DashboardWidget::findOne($widgetId);

        if ($widget === null) {
            throw new NotFoundHttpException('The requested widget does not exist.');
        }

        $this->findModel($widget->dashboard_id);

        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            // Delete associated layout first due to foreign key constraint
            if ($widget->layout) {
                $widget->layout->delete();
            }
            
            if ($widget->delete()) {
                $transaction->commit();
                Yii::$app->response->statusCode = 204;
                return true;
            }
            
            $transaction->rollBack();
            Yii::$app->response->statusCode = 500;
            return false;
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->response->statusCode = 500;
            return [
                'success' => false,
                'message' => 'Failed to delete widget: ' . $e->getMessage()
            ];
        }
    }

    public function actionUpdateWidgetLayout()
    {
        $this->checkAccess();
        $layoutData = Yii::$app->request->post('widgetsPositionalInformation', []);
        
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            $savedLayouts = [];
            
            foreach ($layoutData as $data) {
                $widgetId = $data['widget_id'] ?? null;
                
                if (!$widgetId) {
                    continue;
                }
                
                // Verify the widget exists and belongs to user's dashboard
                $widget = DashboardWidget::findOne($widgetId);
                if (!$widget) {
                    continue;
                }
                
                // Verify ownership through dashboard
                $this->findModel($widget->dashboard_id);
                
                // Find or create layout record for this widget
                $layout = WidgetLayout::findOne(['widget_id' => $widgetId]);
                if (!$layout) {
                    $layout = new WidgetLayout();
                    $layout->widget_id = $widgetId;
                }
                
                // Update layout properties
                $layout->x = $data['x'] ?? 0;
                $layout->y = $data['y'] ?? 0;
                $layout->w = $data['w'] ?? 3;
                $layout->h = $data['h'] ?? 4;
                
                if (!$layout->save()) {
                    throw new \Exception('Failed to save layout for widget ' . $widgetId);
                }
                
                $savedLayouts[] = $layout;
            }
            
            $transaction->commit();
            
            Yii::$app->response->statusCode = 200;
            return [
                'success' => true,
                'message' => 'Widget layouts updated successfully',
                'count' => count($savedLayouts)
            ];
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->response->statusCode = 500;
            return [
                'success' => false,
                'message' => 'Failed to update widget layouts: ' . $e->getMessage()
            ];
        }
    }

    public function actionFilters()
    {
        $this->checkAccess();

        $userId = Yii::$app->user->getId();
        $filters = Filter::find()
            ->where(['user_id' => $userId])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        $safeFilters = array_map(function (Filter $filter) {
            return [
                'id' => $filter->id,
                'name' => $filter->name,
                'time_filter' => $filter->time_filter,
            ];
        }, $filters);

        return $safeFilters;
    }

    protected function findModel($id)
    {
        $model = Dashboard::findOne($id);
        $userId = Yii::$app->user->getId();

        if ($model !== null) {
            // Check ownership
            if ($model->user_id == $userId) {
                return $model;
            } else {
                throw new ForbiddenHttpException('You do not have permission to access this dashboard.');
            }
        } else {
            throw new NotFoundHttpException("The requested dashboard with ID {$id} does not exist.");
        }
    }


    protected function getWidgetsOfDashboard($dashboardId)
    {
        // findModel will throw an exception if the dashboard doesn't exist or doesn't belong to the user
        $this->findModel($dashboardId);

        $widgets = DashboardWidget::find()
            ->where(['dashboard_id' => $dashboardId])
            ->with('layout')
            ->all();

        return array_map(function($widget) {
            $widgetArray = $widget->toArray();
            $widgetArray['layout'] = $widget->layout ? $widget->layout->toArray() : null;
            return $widgetArray;
        }, $widgets);
    }


}