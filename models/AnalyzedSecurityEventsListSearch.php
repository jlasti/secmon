<?php

namespace app\models;

use macgyer\yii2materializecss\widgets\form\Select;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AnalyzedSecurityEventsListSearch represents the model behind the search form about `app\models\AnalyzedSecurityEventsList`.
 */
class AnalyzedSecurityEventsListSearch extends AnalyzedSecurityEventsList
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'events_analyzed_iteration', 'analyzed_security_events_id'], 'integer'],
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
        $query = AnalyzedSecurityEventsList::find()->select(['id'=> 'analyzed_security_events_id']);

        // Get max, maybe it can be done in more optimal way
        $max = AnalyzedSecurityEventsList::find()->max('events_analyzed_iteration');

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
            'events_analyzed_iteration' => $max,
            'analyzed_security_events_id' => $this->analyzed_security_events_id,
        ]);

        $query->orderBy(['analyzed_security_events_id' => SORT_DESC]);

        Yii::$app->cache->flush();

        return $dataProvider;
    }
}