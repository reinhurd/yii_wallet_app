<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\WalletChange;

/**
 * WalletChangeSearch represents the model behind the search form of `app\models\WalletChange`.
 */
class WalletChangeSearch extends WalletChange
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'wallet_id', 'change_value'], 'integer'],
            [['entity_name', 'comment', 'created_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = WalletChange::find();

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
            'wallet_id' => $this->wallet_id,
            'change_value' => $this->change_value,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'entity_name', $this->entity_name])
            ->andFilterWhere(['like', 'comment', $this->comment]);

        return $dataProvider;
    }
}
