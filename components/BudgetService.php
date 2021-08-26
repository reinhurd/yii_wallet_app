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
}
