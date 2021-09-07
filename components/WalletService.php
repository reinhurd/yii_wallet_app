<?php

namespace app\components;

use app\models\Wallet;
use app\models\WalletChange;
use yii\base\InvalidArgumentException;
use yii\base\UnknownPropertyException;
use yii\db\Expression;

/**
 * Viewing Wallet and updating funds in it by some secondary services
 */
class WalletService
{
    private $budgetService;

    public function __construct(BudgetService $budgetService)
    {
        $this->budgetService = $budgetService;
    }

    public function resetWallets(): void
    {
        Wallet::deleteAll();
        WalletChange::deleteAll();
    }

    public function saveWallet(Wallet $wallet): ?Wallet
    {
        //dont keep positive credits
        if ($wallet->money_credits > 0) {
            $wallet->money_everyday += $wallet->money_credits;
            $wallet->money_credits = 0;
        }

        $wallet->last_update_date = new Expression('NOW()');
        $wallet->money_all = $this->budgetService->countMoneyForDayByFunds($wallet);

        if ($wallet->save()) {
            return $wallet;
        }

        return null;
    }

    public function createWalletChange(string $entityName, int $changeValue, string $comment): ?WalletChange
    {
        $newWalletChange = new WalletChange();
        $newWalletChange->entity_name = $entityName;
        $newWalletChange->change_value = $changeValue;
        $newWalletChange->comment = $comment;

        if (!$newWalletChange->save()) {
            return null;
        }

        return $newWalletChange;
    }

    public function beforeSaveWalletChange(WalletChange $walletChange): ?Wallet
    {
        $lastWallet = Wallet::find()->orderBy(['id' => SORT_DESC])->one();
        if (!$lastWallet instanceof Wallet) {
            return null;
        }
        try {
            $entity_name = $walletChange->entity_name;

            //we get old funds and make sum with new value from wallet change
            $changedValue = $lastWallet->{$entity_name};
            $newWallet = new Wallet();
            //todo make something with credits and add some financial helpers here
            $newWallet->attributes = $lastWallet->attributes;
            $newWallet->{$entity_name} = $changedValue + $walletChange->change_value;
        } catch (UnknownPropertyException $exception) {
            return null;
        }

        return $this->saveWallet($newWallet);
    }

    public function setNewWalletToEmptyBase(array $newWalletValues): void
    {
        $this->resetWallets();
        $newWallet = new Wallet();
        $i = 1; //not use 0 value - its command word
        foreach ($newWallet->getAttributes() as $name => $value) {
            if (!isset(Wallet::getFieldByCode()[$i])) {
                continue;
            }
            if ($name !== Wallet::getFieldByCode()[$i]) {
                continue;
            }
            $newWallet->{$name} = $newWalletValues[$i];
            $i++;
        }
        if ($this->saveWallet($newWallet) === null) {
            throw new InvalidArgumentException();
        }
    }

    public function getLastWalletInfo(): ?int
    {
        $lastWallet = Wallet::find()->orderBy(['id' => SORT_DESC])->one();
        if (!$lastWallet instanceof Wallet) {
            throw new InvalidArgumentException();
        }

        return $lastWallet->money_all;
    }
}
