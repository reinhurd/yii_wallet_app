<?php

namespace tests\unit\components;

use app\components\WalletService;
use app\models\repository\WalletRepository;
use app\models\Wallet;
use tests\unit\helpers\BaseHelperTest;

class WalletServiceTest extends BaseHelperTest
{
    private $walletService;

    public function setUp(): void
    {
        parent::setUp();
        $walletRepo = $this->createMock(WalletRepository::class);
        $this->walletService = new WalletService($walletRepo);
    }

    public function testSaveWallet(): void
    {
        $walletMock = $this->createARMock(Wallet::class);
        $walletMock->money_credits = 100;
        $walletMock->money_everyday = 0;
        $walletMock->money_medfond = 0;
        $walletMock->money_long_clothes = 0;
        $walletMock->money_long_gifts = 0;
        $walletMock->money_long_reserves = 0;
        $walletMock->money_long_deposits = 0;
        $walletMock
            ->expects(self::once())
            ->method('save')
            ->willReturn($walletMock);

        $resultWallet = $this->walletService->saveWallet($walletMock);

        $this->assertEquals(100, $resultWallet->money_everyday, 'Money from plused credits saved in everyday');
        $this->assertEquals(100, $resultWallet->money_all, 'Money sums saved in all');
        $this->assertNotEmpty($resultWallet->last_update_date);
    }
}
