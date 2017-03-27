<?php

namespace app\controllers;

use app\models\FilterRule;
use app\models\View;
use Yii;
use app\models\Filter;
use app\models\Event;
use app\models\Filter\FilterSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;

/**
 * FilterController implements the CRUD actions for Filter model.
 */
class FilterController extends Controller
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
     * Lists all Filter models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FilterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Filter model.
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
     * Creates a new Filter model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Filter(['user_id' => Yii::$app->user->id]);

		$rules = $this->_createRulesArray();

		if($this->save($model, $rules))
		{
			return $this->redirect(['view', 'id' => $model->id]);
		}

		return $this->render('create', [
			'model' => $model,
			'rules' => $rules,
		]);
    }

    /**
     * Updates an existing Filter model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
		$model = $this->findModel($id);

		$rules = $this->_createRulesArray($model->rules);

		if($this->save($model, $rules))
		{
			return $this->redirect(['view', 'id' => $model->id]);
		}

		return $this->render('update', [
			'model' => $model,
			'rules' => $rules,
		]);
    }

    /**
     * Deletes an existing Filter model.
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
     * Deletes rule from filter.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteRule($id, $ruleId)
    {
        //todo - remove rule by $ruleId
        return $this->redirect(['update', 'id' => $id]);
    }

    public function actionGetFiltersOfUser($userId)
    {
        return Json::encode(self::getFiltersOfUser($userId));
    }

    public static function getFiltersOfUser($userId)
    {
        $loggedUserId = Yii::$app->user->getId();

        if ( $loggedUserId != intval($userId)) return null;

        $filters = Filter::findAll(['user_id' => $userId]);

        return $filters;
    }

    public function actionAddFilterToComponent($filterId, $componentId)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $loggedUserId = Yii::$app->user->getId();
        $filter = Filter::findOne(['id' => $filterId]);
        $component = View\Component::findOne(['id' => $componentId]);

        if ( empty($filter) || $filter->user_id != $loggedUserId )
            return null;

        if ( !empty($component) )
        {
            $component->filter_id = $filterId;
            $component->update();
            $filteredData = $this->getFilteredEvents($filter->id);

            return [
                'html' => \app\widgets\FilterWidget::widget(['data' => compact('component', 'filter', 'filteredData')])
            ];
        }
        else return null;
    }

    public function actionGetComponentContent($componentId)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $loggedUserId = Yii::$app->user->getId();
        $component = View\Component::findOne(['id' => $componentId]);
        $filter = Filter::findOne(['id' => $component->filter_id]);

        if ( empty($filter) || $filter->user_id != $loggedUserId )
            return null;

        if ( !empty($component) )
        {
            $filteredData = $this->getFilteredEvents($filter->id);

            return [
                'html' => \app\widgets\FilterWidget::widget(['data' => compact('component', 'filter', 'filteredData')])
            ];
        }
        else
            return null;
    }

    public function actionRemoveFilterFromComponent($componentId)
    {
        $loggedUserId = Yii::$app->user->getId();
        $component = View\Component::findOne(['id' => $componentId]);

        if (empty($component)) return null;

        $filter = Filter::findOne(['id' => $component->filter_id]);

        if ( empty($filter) || $filter->user_id != $loggedUserId ) return null;

        if ( !empty($component) )
        {
            $component->filter_id = null;
            $component->update();

            return true;
        }
        else return null;
    }

    public function actionGetFilteredEvents($filterId)
    {
        return Json::encode($this->getFilteredEvents($filterId));
    }

    protected function getFilteredEvents($filterId)
    {
        $query = Event::find();
        $filter = $this->findModel($filterId);
        $filteredData = $query->applyFilter($filter)->all();

        return $filteredData;
    }

    /**
     * Saves filter model and its rules. Used in update and create
     *
     * @param Filter $model Filter to be saved
     * @param FilterRule[] $rules Filters rules
     *
     * @return boolean
     */
    protected function save($model, $rules)
	{
		$loaded = true;

		$loaded &= FilterRule::loadMultiple($rules, Yii::$app->request->post()) && FilterRule::validateMultiple($rules);
		$loaded &= $model->load(Yii::$app->request->post()) && $model->validate();

		if($loaded)
		{
			$model->setRules($rules);

			if($model->save())
			{
				return true;
			}
		}

		return false;
	}

    /**
     * Prepares rules from $_POST
     *
     * @param FilterRule[] $rules Already existing rules, used in update
     *
     * @return FilterRule[]
     */
    protected function _createRulesArray($rules = null)
	{
		$array = $rules ?? [
			new FilterRule(),
		];

//		$postCount = count(Yii::$app->request->post('FilterRule'));
//		$count = $postCount - count($array);

        $count = count(Yii::$app->request->post('FilterRule')) - count($array);

        for($i = 0; $i < $count; $i++)
		{
			$array[] = new FilterRule();
		}

		/**
		 * if rules are already loaded and there are less rules in POST,
		 * it means that a single or multiple rules were deleted,
		 * so slice the array and return only so many rules as defined in POST
		 */
//		if($rules != null && $count < 0)
//		{
//			$delete = array_slice($array, $postCount);
//
//			$transaction = Yii::$app->db->beginTransaction();
//
//			foreach($delete as $rule)
//			{
//				if(!$rule->delete())
//				{
//					$transaction->rollBack();
//
//					throw new \yii\db\Exception('Cannot delete rule');
//				}
//			}
//
//			$transaction->commit();
//
//			$array = array_slice($array, 0, $postCount);
//		}

		return $array;
	}

    /**
     * Finds the Filter model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Filter the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Filter::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
