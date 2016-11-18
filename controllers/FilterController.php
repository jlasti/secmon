<?php

namespace app\controllers;

use app\models\FilterRule;
use Yii;
use app\models\Filter;
use app\models\Filter\FilterSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

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

    protected function _createRulesArray($rules = null)
	{
		$array = $rules ?? [
			new FilterRule(),
		];

		$count = count(Yii::$app->request->post('FilterRule')) - count($array);

		for($i = 0; $i < $count; $i++)
		{
			$array[] = new FilterRule();
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
