<?php

namespace tests\unit\models;

use app\models\Wallet;
use Codeception\Test\Unit;

class WalletTest extends Unit
{
    public function testLastMoneyValue()
    {
        $testValue = random_int(1, 99);
        $testFieldName = Wallet::getFieldByCode()[Wallet::MONEY_EVERYDAY];
        $walletOne = new Wallet();
        $walletOne->{$testFieldName} = $testValue;
        $walletOne->save();

        $walletTwo = new Wallet();
        $walletTwo->save();
        $walletTwo->refresh();

        $this->assertEquals($testValue, $walletTwo->{$testFieldName}, 'field value in next object must be equal on previous object');
    }
}
