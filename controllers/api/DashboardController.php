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
     * Retrieves all Dashboards for the current user.
     * If no dashboards exist, a default one is created.
     *
     * @return array|Dashboard[] The list of Dashboard models.
     * @throws ForbiddenHttpException if not authenticated.
     */
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

    /**
     * Returns a single Dashboard model by ID.
     *
     * @param int $id The ID of the dashboard.
     * @return Dashboard The loaded dashboard model.
     * @throws NotFoundHttpException if the dashboard does not exist.
     * @throws ForbiddenHttpException if not authenticated or not owned by user.
     */
    public function actionDashboard($id)
    {
        $this->checkAccess();
        return $this->findModel($id);
    }

    /**
     * Creates a new Dashboard model.
     *
     * @return Dashboard|array The created Dashboard model or validation errors.
     * @throws ForbiddenHttpException if not authenticated.
     */
    public function actionCreate()
    {
        $this->checkAccess();

        $model = new Dashboard();
        $model->user_id = Yii::$app->user->getId();

        // The old controller logic handled setting the new one as active and deactivating others.
        // We will keep this logic here for consistency.

        // Deactivate all current active dashboards for the user
        Dashboard::updateAll(['active' => 0], ['user_id' => $model->user_id, 'active' => 1]);

        // Load POST data, set new dashboard as active
        $model->load(Yii::$app->request->post(), '');
        $model->active = 1; // Set new one as active

        if ($model->save()) {
            Yii::$app->response->statusCode = 201; // Created
            return $model;
        }

        // Return errors on failure
        Yii::$app->response->statusCode = 422; // Unprocessable Entity
        return $model->errors;
    }

    /**
     * Updates an existing Dashboard model.
     *
     * @param int $id The ID of the dashboard.
     * @return Dashboard|array The updated Dashboard model or validation errors.
     * @throws NotFoundHttpException if the dashboard is not found.
     * @throws ForbiddenHttpException if not authenticated or not owned by user.
     */
    public function actionUpdate($id)
    {
        $this->checkAccess();

        $model = $this->findModel($id);

        $model->active = 1;
        $model->load(Yii::$app->request->post(), '');

        if ($model->save()) {
            return $model;
        }

        // Return errors on failure
        Yii::$app->response->statusCode = 422; // Unprocessable Entity
        return $model->errors;
    }

    /**
     * Deletes an existing Dashboard model.
     *
     * @param int $id The ID of the dashboard.
     * @return bool True on successful deletion.
     * @throws NotFoundHttpException if the dashboard is not found.
     * @throws ForbiddenHttpException if not authenticated or not owned by user.
     */
    public function actionDelete($id)
    {
        $this->checkAccess();
        $model = $this->findModel($id); // This checks ownership

        if ($model->delete()) {
            Yii::$app->response->statusCode = 204; // No Content
            return true;
        }
        // Should rarely happen if findModel and delete() work correctly, but good practice.
        Yii::$app->response->statusCode = 500;
        return false;
    }


    public function actionChangeActive()
    {
        // 1. Get the parameter from the POST body (raw data)
        $newDashboardId = Yii::$app->request->post('newDashboardId');

        if (!$newDashboardId) {
            // Throw a bad request exception if the ID is missing
            throw new \yii\web\BadRequestHttpException('Missing newDashboardId parameter in POST request.');
        }
        $this->checkAccess();
        $userId = Yii::$app->user->getId();
        Dashboard::updateAll(['active' => 0], ['user_id' => $userId]);
        $dashboard = $this->findModel($newDashboardId); 
        // Activate the requested dashboard
        $dashboard->active = 1;
        $dashboard->save(false); 

        return $this->getWidgetsOfDashboard($newDashboardId);
    }

    /**
     * Creates a new Widget for a specific Dashboard.
     *
     * Endpoint: POST /dashboards/create-widget
     * Body: { "dashboard_id": 1, "config": "{...}", "order": 1 }
     *
     * NOTE: The original action accepted parameters via query string, REST prefers body data.
     * This implementation uses body data for config and order, and assumes dashboard_id is also in the body or route.
     *
     * @return array|bool The created widget model or validation errors.
     * @throws ForbiddenHttpException if not authenticated.
     */
    public function actionCreateWidget()
    {
        $this->checkAccess();

        $request = Yii::$app->request;
        $title = $request->post('title');
        $dashboardId = $request->post('dashboard_id');
        $config = $request->post('config');
        $chartType = $request->post('chart_type');

        // Verify the dashboard exists and belongs to the user
        $this->findModel($dashboardId);

        $widget = new DashboardWidget();
        $widget->dashboard_id = $dashboardId;
        $widget->title = $title;
        $widget->chart_type = $chartType;
        $widget->config = $config;

        if ($widget->save()) {
            Yii::$app->response->statusCode = 201; // Created
            // Note: Returning the full widget HTML is non-RESTful, but kept for compatibility with the old controller's intended usage.
            // A pure API would just return the widget model.
            return [
                'widget' => $widget,
                // 'html' => \app\widgets\ComponentWidget::widget(['data' => compact('widget')]), // Removed to keep it RESTful, return data only.
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

        // Basic check: ensure widget's parent dashboard belongs to the user
        $this->findModel($widget->dashboard_id);

        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            // Delete associated layout first due to foreign key constraint
            if ($widget->layout) {
                $widget->layout->delete();
            }
            
            // Now delete the widget
            if ($widget->delete()) {
                $transaction->commit();
                Yii::$app->response->statusCode = 204; // No Content
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
    }    /**
     * Retrieves all Filters for the current user.
     *
     * Endpoint: GET /dashboards/filters
     *
     * @return array|Filter[] The list of Filter models.
     * @throws ForbiddenHttpException if not authenticated.
     */
    public function actionFilters()
    {
        $this->checkAccess();

        $userId = Yii::$app->user->getId();
        $filters = Filter::find()
            ->where(['user_id' => $userId])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        // Return safe array without user_id
        $safeFilters = array_map(function (Filter $filter) {
            return [
                'id' => $filter->id,
                'name' => $filter->name,
                'time_filter' => $filter->time_filter,
            ];
        }, $filters);

        return $safeFilters;
    }

    /**
     * Finds the Dashboard model based on its primary key value and checks ownership.
     *
     * @param int $id
     * @return Dashboard The loaded model
     * @throws NotFoundHttpException if the model cannot be found or does not belong to the user.
     */
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