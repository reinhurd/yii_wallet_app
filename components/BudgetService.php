<?php

namespace app\components;

use app\models\repository\WalletRepository;
use app\models\Wallet;
use yii\base\InvalidArgumentException;

/**
 * Counting money with some rules
 */
class BudgetService
{
    //todo make more clear dump this const for future response about rules
    public const FUNDS_SALARY_WEIGHTS_RULES = [
        Wallet::MONEY_EVERYDAY => 0.4,
        Wallet::MONEY_MEDFOND => 0.1,
        Wallet::MONEY_LONG_CLOTHES => 0.1,
        Wallet::MONEY_LONG_GIFTS => 0.1,
        Wallet::MONEY_LONG_RESERVES => 0.1,
        Wallet::MONEY_LONG_DEPOSITS => 0.1,
        Wallet::MONEY_CREDITS => 0.1
    ];
    private $walletRepository;
    private $walletService;

    public function __construct(
        WalletRepository $walletRepository,
        WalletService $walletService
    ) {
        $this->walletRepository = $walletRepository;
        $this->walletService = $walletService;
    }

    public static function getFundsWeightDescription(): string
    {
        $result = '';
        foreach (self::FUNDS_SALARY_WEIGHTS_RULES as $fieldCode => $value) {
            $result .= chr(10) . Wallet::getFieldByCode()[$fieldCode] . ' => coef ' . $value;
        }

        return $result;
    }

    public function getMoneyForCurrentMonth(int $daysInMonthRemaining): float
    {
        $lastWallet = $this->walletRepository->getLastWallet();
        if (!$lastWallet instanceof Wallet) {
            throw new InvalidArgumentException();
        }

        return round($lastWallet->money_everyday / $daysInMonthRemaining);
    }

    public function setSalary(int $salary): void
    {
        foreach (self::FUNDS_SALARY_WEIGHTS_RULES as $fundName => $ruleValue) {
            $entityName = Wallet::getFieldByCode()[(int)$fundName] ?? null;
            if (empty($entityName)) {
                throw new InvalidArgumentException();
            }
            $changeValue = (int)round($salary * $ruleValue);
            $comment = "Доля от зарплаты {$salary} в размере {$changeValue}";
            $this->walletService->createWalletChange($entityName, $changeValue, $comment);
        }
    }
}
