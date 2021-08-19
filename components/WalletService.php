<?php

namespace app\components;

use app\models\Wallet;
use app\models\WalletChange;
use yii\base\InvalidArgumentException;

class WalletService
{
    public function resetWallets(): void
    {
        Wallet::deleteAll();
        WalletChange::deleteAll();
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
