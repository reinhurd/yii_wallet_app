<?php

namespace tests\unit\models;

use app\models\Wallet;
use Codeception\Test\Unit;

class WalletTest extends Unit
{
    public function testBeforeSave()
    {
        $testValueOne = 100;
        $testValueTwo = 200;
        $testSum = $testValueOne + $testValueTwo;
        $wallet = new Wallet();

        $wallet->money_everyday = $testValueOne;
        $wallet->money_medfond = $testValueTwo;
        $wallet->save();

        $this->assertEquals($testSum, $wallet->money_all, 'money_all field is equal all sum on wallet');
    }
}
