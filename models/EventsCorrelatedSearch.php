<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\EventsCorrelated;

/**
 * EventsCorrelatedSearch represents the model behind the search form about `app\models\EventsCorrelated`.
 */
class EventsCorrelatedSearch extends EventsCorrelated
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'cef_event_class_id', 'cef_severity', 'parent_events'], 'integer'],
            [['datetime', 'host', 'cef_version', 'cef_vendor', 'cef_dev_prod', 'cef_dev_version', 'cef_name', 'raw'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = EventsCorrelated::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'datetime' => $this->datetime,
            'cef_event_class_id' => $this->cef_event_class_id,
            'cef_severity' => $this->cef_severity,
            'parent_events' => $this->parent_events,
        ]);

        $query->andFilterWhere(['like', 'host', $this->host])
            ->andFilterWhere(['like', 'cef_version', $this->cef_version])
            ->andFilterWhere(['like', 'cef_vendor', $this->cef_vendor])
            ->andFilterWhere(['like', 'cef_dev_prod', $this->cef_dev_prod])
            ->andFilterWhere(['like', 'cef_dev_version', $this->cef_dev_version])
            ->andFilterWhere(['like', 'cef_name', $this->cef_name])
            ->andFilterWhere(['like', 'raw', $this->raw]);

        $query->orderBy(['datetime' => SORT_DESC]);

        Yii::$app->cache->flush();

        return $dataProvider;
    }
}
