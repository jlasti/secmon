<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\NetworkModel;

/**
 * NetworkModelSearch represents the model behind the search form about `app\models\NetworkModel`.
 */
class NetworkModelSearch extends NetworkModel
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['ip_address', 'mac_address', 'description', 'hostname', 'operation_system', 'open_ports', 'ports', 'services', 'vulnerabilities'], 'safe'],
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
     * @throws \yii\base\InvalidConfigException
     */
    public function search($params)
    {
        $query = NetworkModel::find();

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
        ]);

        $query->andFilterWhere(['like', 'ip_address', $this->ip_address])
            ->andFilterWhere(['like', 'mac_address', $this->mac_address])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'hostname', $this->hostname])
            ->andFilterWhere(['like', 'operation_system', $this->operation_system])
            ->andFilterWhere(['like', 'open_ports', $this->open_ports])
            ->andFilterWhere(['like', 'ports', $this->ports])
            ->andFilterWhere(['like', 'vulnerabilities', $this->vulnerabilities]);
            
        Yii::$app->cache->flush();

        return $dataProvider;
    }
}