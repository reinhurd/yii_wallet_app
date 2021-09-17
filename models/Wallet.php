<?php

namespace app\models;

use yii\db\ActiveRecord;

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
class Wallet extends ActiveRecord
{
    //todo make const for every field name
    const MONEY_EVERYDAY = 1;
    const MONEY_MEDFOND = 2;
    const MONEY_LONG_CLOTHES = 3;
    const MONEY_LONG_GIFTS = 4;
    const MONEY_LONG_RESERVES = 5;
    const MONEY_LONG_DEPOSITS = 6;
    const MONEY_CREDITS = 7;

    private const ZERO_MONEY_WHEN_NO_INFO = 0;

    public static function tableName()
    {
        return 'wallet';
    }

    public static function getFieldByCode()
    {
        return [
            self::MONEY_EVERYDAY => 'money_everyday',
            self::MONEY_MEDFOND => 'money_medfond',
            self::MONEY_LONG_CLOTHES => 'money_long_clothes',
            self::MONEY_LONG_GIFTS => 'money_long_gifts',
            self::MONEY_LONG_RESERVES => 'money_long_reserves',
            self::MONEY_LONG_DEPOSITS => 'money_long_deposits',
            self::MONEY_CREDITS => 'money_credits'
        ];
    }

    public static function getFieldByCodeDescription()
    {
        $result = '';
        foreach (self::getFieldByCode() as $code => $text) {
            $result .= '
            ' . $text . ' => ' . $code;
        }

        return $result;
    }

    public function rules()
    {
        return [
            [['money_all', 'money_everyday', 'money_medfond', 'money_long_clothes', 'money_long_gifts', 'money_long_reserves', 'money_long_deposits', 'money_credits'], 'integer'],
            ['money_everyday', 'default', 'value' => $this->getLastMoneyValue('money_everyday')],
            ['money_medfond', 'default', 'value' => $this->getLastMoneyValue('money_medfond')],
            ['money_long_clothes', 'default', 'value' => $this->getLastMoneyValue('money_long_clothes')],
            ['money_long_gifts', 'default', 'value' => $this->getLastMoneyValue('money_long_gifts')],
            ['money_long_reserves', 'default', 'value' => $this->getLastMoneyValue('money_long_reserves')],
            ['money_long_deposits', 'default', 'value' => $this->getLastMoneyValue('money_long_deposits')],
            ['money_credits', 'default', 'value' => $this->getLastMoneyValue('money_credits')],
            [['last_update_date'], 'safe'],
        ];
    }

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

    private function getLastMoneyValue(string $fieldName): int
    {
        $lastEntry = Wallet::find()->orderBy(['id' => SORT_DESC])->one();
        if (!$lastEntry instanceof Wallet) {
            return self::ZERO_MONEY_WHEN_NO_INFO;
        }

        return $lastEntry->{$fieldName};
    }
}
