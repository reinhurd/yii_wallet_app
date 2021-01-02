<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Wallet;

/**
 * WalletSearch represents the model behind the search form of `app\models\Wallet`.
 */
class WalletSearch extends Wallet
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'money_all', 'money_everyday', 'money_medfond', 'money_long_clothes', 'money_long_gifts', 'money_long_reserves', 'money_long_deposits', 'money_credits'], 'integer'],
            [['last_update_date'], 'safe'],
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
        $query = Wallet::find();

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
            'money_all' => $this->money_all,
            'money_everyday' => $this->money_everyday,
            'money_medfond' => $this->money_medfond,
            'money_long_clothes' => $this->money_long_clothes,
            'money_long_gifts' => $this->money_long_gifts,
            'money_long_reserves' => $this->money_long_reserves,
            'money_long_deposits' => $this->money_long_deposits,
            'money_credits' => $this->money_credits,
            'last_update_date' => $this->last_update_date,
        ]);

        return $dataProvider;
    }
}
