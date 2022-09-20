<?php

namespace app\controllers;

use app\models\SecurityEvents;
use app\models\FilterRule;
use app\models\View;
use app\models\SecurityEventsPage;
use Yii;
use app\models\Filter;
use app\models\Filter\FilterSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\filters\AccessControl;

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
            'access' => [
                'class'=> AccessControl::className(),
                'rules' => [
                    [
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
        $url =  $_SERVER['REQUEST_URI'];
        $model = new Filter(['user_id' => Yii::$app->user->id]);

		$rules = $this->_createRulesArray();
        
		if($this->save($model, $rules))
		{
            if(str_contains($url, 'securityEventsPage=1')){
                $userId = Yii::$app->user->getId();
                $securityEventsPage = SecurityEventsPage::findOne(['user_id' => $userId]);
                
                if(!empty($securityEventsPage) && $model->id){
                    $securityEventsPage->filter_id = $model->id;
                    $securityEventsPage->update();
                }
                return $this->redirect(['security-events/index']);
            }
                
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
        $url =  $_SERVER['REQUEST_URI'];
		$model = $this->findModel($id);

        $postRules = Yii::$app->request->post('FilterRule');
        if (Yii::$app->request->isPost && ($postRules === null || count($postRules) === 0)) {
            $model->delete();
            return $this->redirect('');
        }
        else
        {
            $rules = $this->_createRulesArray($model->rules, $postRules);

            if ($this->save($model, $rules))
            {
                if(str_contains($url, 'securityEventsPage=1'))
                    return $this->redirect(['security-events/index']);
      			return $this->redirect(['view', 'id' => $model->id]);
            }

            return $this->render('update', [
                'model' => $model,
                'rules' => $rules,
            ]);
        }
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

        $filters = Filter::findAll(['user_id' => $userId, 'time_filter' => false]);

        return $filters;
    }

    public function actionAddFilterToComponent($filterId, $componentId, $contentTypeId, $dataTypeParameter)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $loggedUserId = Yii::$app->user->getId();
        $filter = Filter::findOne(['id' => $filterId]);
        $component = View\Component::findOne(['id' => $componentId]);

        if ( empty($filter) || $filter->user_id != $loggedUserId )
            return null;

        if ( !empty($component) )
        {
            // check config
            if (!empty($contentTypeId) && !empty($dataTypeParameter))
            {
                if ($contentTypeId == 'table')
                {
                    $dbCols = array_keys(SecurityEvents::columns());
                    $cols = explode(',', $dataTypeParameter);
                    $res = [];
                    foreach ($cols as $idx => $col)
                    {
                        if (in_array($col, $dbCols))
                        {
                            $res[] = $col;
                        }
                    }
                    $dataTypeParameter = implode(',', $res);
                }
            }

            $component->filter_id = $filterId;
            $component->data_type = $contentTypeId;
            $component->data_param = $dataTypeParameter;
            $component->update();

            switch ($contentTypeId) {
                case "pieChart":
                    return [
                        'contentTypeId' => $contentTypeId,
                        'dataTypeParameter' => $dataTypeParameter,
                        'data' => Json::encode($this->getFilteredEventsPieGraph($filter->id, $dataTypeParameter))
                    ];
                    break;
                case "barChart":
                    return [
                        'contentTypeId' => $contentTypeId,
                        'dataTypeParameter' => $dataTypeParameter,
                        'data' => Json::encode($this->getFilteredEventsBarGraph($filter->id, $dataTypeParameter))
                    ];
                    break;
                case "table":
                    unset($filteredData);
                    $filteredData = $this->getFilteredEvents($filter->id, 1);
                    $pages = $this->getFilteredEventsCount($filter->id);

                    if (!empty($dataTypeParameter)) {
                        $columns = explode(',', $dataTypeParameter);
                        $columns = array_merge(['id'], $columns);
                    }
                    else
                        $columns = ['id', 'datetime', 'device_host_name', 'application_protocol'];

                    return [
                        'contentTypeId' => $contentTypeId,
                        'dataTypeParameter' => $dataTypeParameter,
                        'paging' => $pages,
                        'html' => \app\widgets\FilterWidget::widget(['data' => compact('component', 'filter', 'filteredData', 'columns')])
                    ];
                    break;
            }
        }
        else return null;
    }

    public function actionGetComponentContent($componentId, $pagination)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $loggedUserId = Yii::$app->user->getId();
        $component = View\Component::findOne(['id' => $componentId]);
        $filter = Filter::findOne(['id' => $component->filter_id]);

        if ( empty($filter) || $filter->user_id != $loggedUserId )
            return null;

        if ( !empty($component) )
        {
            $contentTypeId =  $component->data_type;
            $dataTypeParameter =  $component->data_param ?? "";

            switch ($contentTypeId) {
                case "pieChart":
                    return [
                      'contentTypeId' => $contentTypeId,
                      'dataTypeParameter' => $dataTypeParameter,
                      'data' => Json::encode($this->getFilteredEventsPieGraph($filter->id, $dataTypeParameter))
                    ];
                    break;
                case "barChart":
                    return [
                      'contentTypeId' => $contentTypeId,
                      'dataTypeParameter' => $dataTypeParameter,
                      'data' => Json::encode($this->getFilteredEventsBarGraph($filter->id, $dataTypeParameter))
                    ];
                    break;
                case "table":
                    unset($filteredData);
                    $filteredData = $this->getFilteredEvents($filter->id, $pagination);
                    $pages = $this->getFilteredEventsCount($filter->id);

                    if (!empty($dataTypeParameter)) {
                        $columns = explode(',', $dataTypeParameter);
                        $columns = array_merge(['id'], $columns);
                    }
                    else
                        $columns = ['id', 'datetime', 'device_host_name', 'application_protocol'];

                    return [
                        'contentTypeId' => $contentTypeId,
                        'dataTypeParameter' => $dataTypeParameter,
                        'paging' => $pages,
                        'html' => \app\widgets\FilterWidget::widget(['data' => compact('filteredData', 'columns')])
                    ];
                    break;
            }
        }
        else return null;
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
        return Json::encode($this->getFilteredEvents($filterId, 1));
    }

    protected function getFilteredEvents($filterId, $page)
    {
        unset($filteredData);
        $query = SecurityEvents::find();
        $page -= 1;

        $filter = $this->findModel($filterId);
        //$timefilter = $this->findModel($timeFilterId);
        $filteredData = $query
                        ->applyFilter($filter)
                        ///->applyFilter($timefilter)
                        ->orderBy([ 'datetime' => SORT_DESC, 'id' => SORT_DESC ])
                        ->limit(10)
                        ->offset(10 * $page)
                        ->all();

        Yii::$app->cache->flush();

        return $filteredData;
    }

    protected function getFilteredEventsCount($filterId)
    {
        unset($filteredData);
        $query = SecurityEvents::find();

        $filter = $this->findModel($filterId);

        $query->select(["count(*) as count"])
            ->applyFilter($filter);

        $filteredData = $query->asArray()->all();

        Yii::$app->cache->flush();

        return $filteredData;
    }

    public function actionGetFilteredEventsBarGraph($filterId)
    {
        return Json::encode($this->getFilteredEventsBarGraph($filterId));
    }

    protected function getFilteredEventsPieGraph($filterId, $dataTypeParameter) {
        unset($filteredData);
        unset($graphData);

        $filter = $this->findModel($filterId);

        $query = SecurityEvents::find();
        $label = "CAST(" . $dataTypeParameter . " AS text) as label";
        $value = "count(" . $dataTypeParameter . ") as count";
        $query->select([$label, $value])
            ->groupby(["label"])
            ->orderBy(['label' => SORT_ASC])
            ->applyFilter($filter);

        $filteredData = $query->asArray()->all();
        Yii::$app->cache->flush();

        return $filteredData;
    }

    protected function getFilteredEventsBarGraph($filterId, $dataTypeParameter = null)
    {
        unset($filteredData);
        unset($graphData);
        $range = 'P1D';

        if (!empty($dataTypeParameter)) {
            $timeUnit = substr($dataTypeParameter, -1);
            if($timeUnit == 'Y' || $timeUnit == 'M' || $timeUnit == 'W' || $timeUnit == 'D') {
                $range = 'P' . $dataTypeParameter;
            } else if ($timeUnit == 'm') {
                $range = 'PT' . substr($dataTypeParameter, 0, -1) . 'M';
            } else {
                $range = 'PT' . $dataTypeParameter;
            }
        }

        $dt = new \DateTime();
        $dt->setTimezone(new \DateTimeZone('Europe/Bratislava'));
        $dt->sub(new \DateInterval($range));
        $date = date_format($dt,"Y-m-d H:i:s");

        $filter = $this->findModel($filterId);

        $query = SecurityEvents::find();
        $query->select(["to_char(datetime,'YYYY-DD-MM HH24:00') as x", "count(to_char(datetime,'HH24 MM-DD-YYYY')) as y"])
            ->groupBy(["x"])
            ->orderBy([ 'x' => SORT_ASC ])
            ->applyFilter($filter)
            ->andWhere(['>', "datetime", $date]);

        $filteredData = $query->asArray()->all();
        Yii::$app->cache->flush();
        $graphData = array();

        $now = new \DateTime();
        $i = 0;
        while ($dt <= $now)
        {
            $str = date_format($dt, 'Y-d-m H:00');
            $formatted = date_format($dt, 'H:00 m-d');

            if (count($filteredData) > 0 and strcmp($str, $filteredData[$i]['x']) === 0) {
                $graphData[] = ['x' => $formatted, 'y' => intval($filteredData[$i]['y'])];
                $i++;
            }
            else {
                $graphData[] = ['x' => $formatted, 'y' => 0];
            }

            $dt->add(new \DateInterval('PT1H'));
        }

        unset($filteredData);

        return $graphData;
    }

    protected function checkFilterForDateRule($filter){
        $rules = $filter->getRules()->all();

        foreach ($rules as $key => $value) {
            $attributes = $value->getAttributes();
            if ($attributes['type'] == 'date') return true;
        }

        return false;
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
     * @param FilterRule[] $postFilters Rulest sent from view
     * @return FilterRule[]
     * @throws \yii\db\Exception
     */
    protected function _createRulesArray($rules = null, $postFilters = null)
	{
		$array = $rules ?? [
			new FilterRule(),
		];

		if ($postFilters === null)
		    $postFilters = Yii::$app->request->post('FilterRule');

//		die(var_dump($postFilters) . "<br>". var_dump($array));

		/**
		 * if rules are already loaded and there are less rules in POST,
		 * it means that a single or multiple rules were deleted,
		 * so slice the array and return only so many rules as defined in POST
		 */
		if(Yii::$app->request->isPost && $rules != null && $postFilters != null)
		{
			$transaction = Yii::$app->db->beginTransaction();
			$deleteKeys = [];

			foreach($array as $deleteKey => $rule)
			{
			    if ($rule->id != null)
			    {
                    $key = array_search($rule->id, array_column($postFilters, 'id'));
                    if ($key === false)
                    {
                        if (!$rule->delete())
                        {
                            $transaction->rollBack();

                            throw new \yii\db\Exception('Cannot delete rule');
                        }
                        $deleteKeys[] = $deleteKey;
                    }
                }
			}

			rsort($deleteKeys);
			foreach ($deleteKeys as $deleteKey)
                array_splice($array, $deleteKey, 1);

			$transaction->commit();
		}

    // fit the length of array
    if($postFilters)
    {
        $count = count($postFilters) - count($array);
        for($i = 0; $i < $count; $i++)
        {
            $array[] = new FilterRule();
        }
    }
        
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
