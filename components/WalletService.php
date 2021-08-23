<?php

namespace app\components;

use app\models\Wallet;
use app\models\WalletChange;
use yii\base\InvalidArgumentException;

/**
 * Viewing Wallet and updating funds in it by some secondary services
 */
class WalletService
{
    public function resetWallets(): void
    {
        Wallet::deleteAll();
        WalletChange::deleteAll();
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
        if (!$newWallet->save()) {
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
