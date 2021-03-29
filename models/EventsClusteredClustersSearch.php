<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * EventsClusteredSearch represents the model behind the search form about `app\models\EventsClustered`.
 */
class EventsClusteredClustersSearch extends EventsClusteredClusters
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['severity','comment','fk_run_id','number_of_events'], 'safe'],
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
        $query = EventsClusteredClusters::find();

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
            'severity' => $this->severity,
            'event_id' => $this->comment,
            'fk_run_id' => $_GET['run_id'],
            'number_of_events' => $this->number_of_events,
        ]);
        $query->orderBy(['id' => SORT_ASC]);

        Yii::$app->cache->flush();

        return $dataProvider;
    }
}

