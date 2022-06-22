<?php

namespace app\models;

use yii\db\Query;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\EventsNormalized;

/**
 * EventsClusteredEventsSearch represents the model behind the search form about `app\models\EventsClusteredEvetns`.
 */
class EventsClusteredEventsSearch extends EventsClusteredEvents
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
             [['id', 'cef_severity', 'src_port', 'dst_port'], 'integer'],
             [['datetime', 'host', 'cef_version', 'cef_vendor', 'cef_dev_prod', 'cef_dev_version', 'cef_name', 'src_ip', 'dst_ip', 'protocol', 'src_mac', 'dst_mac', 'extensions', 'raw', 'src_country', 'dst_country', 'src_city', 'dst_city', 'src_latitude', 'dst_latitude', 'src_longitude', 'dst_longitude'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // typass scenarios() implementation in the parent class
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

        $query = EventsNormalized::find()->leftJoin('clustered_events_relations', 'clustered_events_relations.fk_event_id=events_normalized.id');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['datetime' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'fk_cluster_id'=> preg_replace('/[^0-9]/','',$_GET['cluster_id']),
        ]);

        Yii::$app->cache->flush();

        return $dataProvider;
    }
}

