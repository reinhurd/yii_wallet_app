<?php

namespace tests\unit\components;

use app\components\BudgetService;
use app\components\WalletService;
use app\models\repository\WalletRepository;
use app\models\Wallet;

class BudgetServiceTest extends BaseHelperTest
{
    private $budgetService;
    private $walletService;
    private $walletRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->walletRepository = $this->createMock(WalletRepository::class);
        $this->walletService = $this->createMock(WalletService::class);
        $this->budgetService = new BudgetService($this->walletRepository, $this->walletService);
    }

    public function testGetFundsWeightDescription(): void
    {
        $result = $this->budgetService::getFundsWeightDescription();

        $this->assertNotEmpty($result);
        $this->assertStringContainsString(' => coef ',$result);
    }
}
