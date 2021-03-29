<?php

namespace app\models;

use yii\db\Query;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\EventsNormalized;

/**
 * EventsClusteredSearch represents the model behind the search form about `app\models\EventsClustered`.
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

        $query = EventsNormalized::find()->leftJoin('clustered_events_relations', 'clustered_events_relations.fk_event_id=events_normalized.id')->where(['=', 'clustered_events_relations.fk_cluster_id', $_GET['cluster_id']]);

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
            // 'datetime' => $this->datetime,
            'cef_event_class_id' => $this->cef_event_class_id,
            'cef_severity' => $this->cef_severity,
            'src_port' => $this->src_port,
            'dst_port' => $this->dst_port,
        ]);

        $query->andFilterWhere(['>=', 'datetime', $this->datetime]);

        $query->andFilterWhere(['like', 'host', $this->host])
            ->andFilterWhere(['like', 'cef_version', $this->cef_version])
            ->andFilterWhere(['like', 'cef_vendor', $this->cef_vendor])
            ->andFilterWhere(['like', 'cef_dev_prod', $this->cef_dev_prod])
            ->andFilterWhere(['like', 'cef_dev_version', $this->cef_dev_version])
            ->andFilterWhere(['like', 'cef_name', $this->cef_name])
            ->andFilterWhere(['like', 'src_ip', $this->src_ip])
            ->andFilterWhere(['like', 'dst_ip', $this->dst_ip])
            ->andFilterWhere(['like', 'protocol', $this->protocol])
            ->andFilterWhere(['like', 'src_mac', $this->src_mac])
            ->andFilterWhere(['like', 'dst_mac', $this->dst_mac])
            ->andFilterWhere(['like', 'src_country', $this->src_country])
            ->andFilterWhere(['like', 'dst_country', $this->dst_country])
            ->andFilterWhere(['like', 'src_city', $this->src_city])
            ->andFilterWhere(['like', 'dst_city', $this->dst_city])
            ->andFilterWhere(['like', 'src_latitude', $this->src_latitude])
            ->andFilterWhere(['like', 'dst_latitude', $this->dst_latitude])
            ->andFilterWhere(['like', 'src_longitude', $this->src_longitude])
            ->andFilterWhere(['like', 'dst_longitude', $this->dst_longitude])
            ->andFilterWhere(['like', 'extensions', $this->extensions])
            ->andFilterWhere(['like', 'raw', $this->raw]);

        $query->orderBy(['datetime' => SORT_DESC]);

        Yii::$app->cache->flush();

        return $dataProvider;
    }
}

