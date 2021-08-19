<?php

namespace app\components;

use app\models\Wallet;
use app\models\WalletChange;

class WalletService
{
    public function resetWallets(): void
    {
        Wallet::deleteAll();
        WalletChange::deleteAll();
    }
}
