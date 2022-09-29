<?php

namespace app\models;

use yii\db\Query;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\SecurityEvents;

/**
 * EventsClusteredEventsSearch represents the model behind the search form about `app\models\EventsClusteredEvents`.
 */
class EventsClusteredEventsSearch extends EventsClusteredEvents
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
             [['id', 'cef_severity', 'source_port', 'destination_port'], 'integer'],
             [['datetime', 'device_host_name', 'cef_version', 'cef_vendor', 'cef_device_product', 'cef_device_version', 'cef_name', 'source_address', 'destination_address', 'application_protocol', 'source_mac_address', 'destiantion_mac_address', 'extensions', 'raw_event', 'source_country', 'destination_country', 'source_city', 'destination_city', 'sorce_geo_latitude', 'destination_geo_latitude', 'source_geo_longitude', 'destination_geo_longitude'], 'safe'],
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

        $query = SecurityEvents::find()->leftJoin('clustered_events_relations', 'clustered_events_relations.fk_event_id=security_events.id');

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

