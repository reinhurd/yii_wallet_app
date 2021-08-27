<?php

namespace app\components;

use app\models\Wallet;
use yii\db\Expression;

/**
 * Counting money with some rules
 */
class BudgetService
{
    public function countMoneyForDayByFunds(Wallet $wallet): int
    {
        return array_sum([
            $wallet->money_credits, //todo change credits work scheme
            $wallet->money_everyday,
            $wallet->money_medfond,
            $wallet->money_long_clothes,
            $wallet->money_long_gifts,
            $wallet->money_long_reserves,
            $wallet->money_long_deposits
        ]);
    }

    public function getMoneyForCurrentMonth(): float
    {
        $lastWallet = Wallet::find()->orderBy(['id' => SORT_DESC])->one();
        $timestamp = date('Y-m-d');
        $daysInMonth = (int)date('t', strtotime($timestamp));
        $thisDayInMonth = (int)date('j', strtotime($timestamp));
        $daysRemaining = $daysInMonth - $thisDayInMonth;

        return round($lastWallet->money_everyday / $daysRemaining);
    }

    public function getSalary()
    {
        //todo create method about get salary and divide it to funds
    }
}
