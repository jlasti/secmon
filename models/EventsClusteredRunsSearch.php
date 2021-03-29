<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * EventsClusteredRunsSearch represents the model behind the search form about `app\models\EventsClusteredRuns`.
 */
class EventsClusteredRunsSearch extends EventsClusteredRuns
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['datetime','type_of_algorithm','number_of_clusters','comment'], 'safe'],
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
        $query = EventsClusteredRuns::find();

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
            'type_of_algorithm' => $this->type_of_algorithm,
            'number_of_clusters' => $this->number_of_clusters,
            'comment' => $this->comment,
        ]);

        $query->orderBy(['id' => SORT_ASC]);

        Yii::$app->cache->flush();

        return $dataProvider;
    }
}
