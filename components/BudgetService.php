<?php

namespace app\components;

use app\models\Wallet;
use yii\base\InvalidArgumentException;

/**
 * Counting money with some rules
 */
class BudgetService
{
    private const FUNDS_SALARY_WEIGHTS_RULES = [
        Wallet::MONEY_MEDFOND => 0.1,
        Wallet::MONEY_LONG_CLOTHES => 0.1,
        Wallet::MONEY_LONG_GIFTS => 0.1,
        Wallet::MONEY_LONG_RESERVES => 0.1,
        Wallet::MONEY_LONG_DEPOSITS => 0.1,
        Wallet::MONEY_CREDITS => 0.1
    ];
    private $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

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

    public function setSalary(int $salary): void
    {
        foreach (self::FUNDS_SALARY_WEIGHTS_RULES as $fundName => $ruleValue) {
            $entityName = Wallet::getFieldByCode()[(int)$fundName] ?? null;
            if (empty($entityName)) {
                throw new InvalidArgumentException();
            }
            $changeValue = round($salary * $ruleValue);
            $comment = "Доля от зарплаты {$salary} в размере {$changeValue}";
            $this->walletService->createWalletChange($entityName, $changeValue, $comment);
        }
    }
}
