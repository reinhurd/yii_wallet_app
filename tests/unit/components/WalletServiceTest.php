<?php

namespace tests\unit\components;

use app\components\WalletService;
use app\models\repository\WalletChangeRepository;
use app\models\repository\WalletRepository;
use app\models\Wallet;
use app\models\WalletChange;

class WalletServiceTest extends BaseHelperTest
{
    private $walletService;
    private $walletRepository;
    private $walletChangeRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->walletRepository = $this->createMock(WalletRepository::class);
        $this->walletChangeRepository = $this->createMock(WalletChangeRepository::class);
        $this->walletService = new WalletService($this->walletRepository, $this->walletChangeRepository);
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

    public function testGetLastWalletInfo(): void
    {
        $walletMock = $this->createARMock(Wallet::class);
        $walletMock->money_all = 100;
        $this->walletRepository
            ->expects(self::once())
            ->method('getLastWallet')
            ->willReturn($walletMock);

        $result = $this->walletService->getLastWalletInfo();

        $this->assertEquals($walletMock->money_all, $result);
    }

    public function testGetBeforeSaveWalletChange(): void
    {
        $walletMock = $this->createARMock(Wallet::class);
        $walletMock->money_medfond = 100;
        $this->walletRepository
            ->expects(self::once())
            ->method('getLastWallet')
            ->willReturn($walletMock);

        $walletChangeMock = $this->createARMock(WalletChange::class);
        $walletChangeMock->entity_name = Wallet::getFieldByCode()[Wallet::MONEY_MEDFOND];
        $walletChangeMock->change_value = 100;

        $result = $this->walletService->getBeforeSaveWalletChange($walletChangeMock);
        $expected = $walletMock->money_medfond + $walletChangeMock->change_value;

        $this->assertEquals($expected, $result->money_medfond);
    }

    public function testResetWallets(): void
    {
        $this->walletRepository
            ->expects(self::once())
            ->method('deleteAllWallet');
        $this->walletChangeRepository
            ->expects(self::once())
            ->method('deleteAllWalletChange');

        $this->walletService->resetWallets();
        $this->assertTrue(true);
    }
}
