<?php

namespace app\models\Filter;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Filter;

/**
 * FilterSearch represents the model behind the search form about `app\models\Filter`.
 */
class FilterSearch extends Filter
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id'], 'integer'],
            [['name'], 'safe'],
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
        $query = Filter::find();
        $userId = Yii::$app->user->getId();

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

        // Show only event filters without time filters
        $query->andFilterWhere([
            'user_id' => $userId,
            'time_filter' => false,
        ]);

        $query->andFilterWhere([
            'user_id' => $userId,
        ]);

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
