<?php

namespace app\models\SecRule;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\SecRule;

/**
 * SecRuleSearch represents the model behind the search form about `app\models\SecRule`.
 */
class SecRuleSearch extends SecRule
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name', 'link', 'type'], 'safe'],
            [['state'], 'boolean'],
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
        $query = SecRule::find();

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

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'link', $this->link])
            ->andFilterWhere(['state' => $this->state])
            ->andFilterWhere(['type' => $this->type])
            ->addOrderBy("id asc");

        return $dataProvider;
    }
}
