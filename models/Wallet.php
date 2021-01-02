<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "wallet".
 *
 * @property int $id
 * @property int|null $money_all
 * @property int|null $money_everyday
 * @property int|null $money_medfond
 * @property int|null $money_long_clothes
 * @property int|null $money_long_gifts
 * @property int|null $money_long_reserves
 * @property int|null $money_long_deposits
 * @property int|null $money_credits
 * @property string|null $last_update_date
 */
class Wallet extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wallet';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['money_all', 'money_everyday', 'money_medfond', 'money_long_clothes', 'money_long_gifts', 'money_long_reserves', 'money_long_deposits', 'money_credits'], 'integer'],
            [['last_update_date'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'money_all' => 'Money All',
            'money_everyday' => 'Money Everyday',
            'money_medfond' => 'Money Medfond',
            'money_long_clothes' => 'Money Long Clothes',
            'money_long_gifts' => 'Money Long Gifts',
            'money_long_reserves' => 'Money Long Reserves',
            'money_long_deposits' => 'Money Long Deposits',
            'money_credits' => 'Money Credits',
            'last_update_date' => 'Last Update Date',
        ];
    }
}
