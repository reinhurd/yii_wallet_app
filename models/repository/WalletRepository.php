<?php

namespace app\models\repository;

use app\models\Wallet;
use yii\db\ActiveQuery;

class WalletRepository
{
    public function getById(int $walletId): ?Wallet
    {
        return $this->find()->where(['id' => $walletId])->one();
    }

    public function getLastWallet(): ?Wallet
    {
        return $this->find()->orderBy(['id' => SORT_DESC])->one();
    }

    private function find(): ActiveQuery
    {
        return Wallet::find();
    }
}
