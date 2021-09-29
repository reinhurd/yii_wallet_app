<?php

namespace app\models\repository;

use app\models\Wallet;
use app\models\WalletChange;
use yii\db\ActiveQuery;

class WalletChangeRepository extends ActiveQuery
{
    public function deleteAllWalletChange(): void
    {
        WalletChange::deleteAll();
    }
}
