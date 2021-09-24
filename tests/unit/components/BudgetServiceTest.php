<?php

namespace tests\unit\components;

use app\components\BudgetService;
use app\components\WalletService;
use app\models\repository\WalletRepository;
use app\models\Wallet;
use yii\base\InvalidArgumentException;

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

    public function testGetMoneyForCurrentMonth(): void
    {
        $lastWalletMock = $this->createARMock(Wallet::class);
        $lastWalletMock->money_everyday = 10000;

        $daysRemaining = 10;

        $this->walletRepository
            ->expects(self::once())
            ->method('getLastWallet')
            ->willReturn($lastWalletMock);

        $expected = (float)(10000 / 10);
        $result = $this->budgetService->getMoneyForCurrentMonth($daysRemaining);

        $this->assertEquals($expected, $result);
    }

    public function testSetSalary()
    {
        $salary = 100000;
        $k = 0;
        foreach (BudgetService::FUNDS_SALARY_WEIGHTS_RULES as $fundName => $ruleValue) {
            $entityName = Wallet::getFieldByCode()[(int)$fundName] ?? null;
            $changeValue = (int)round($salary * $ruleValue);
            $comment = "Доля от зарплаты {$salary} в размере {$changeValue}";
            $this->walletService
                ->expects(self::at($k))
                ->method('createWalletChange')
                ->with($entityName, $changeValue, $comment)
                ->willReturn(null);
            $k++;
        }

        $this->budgetService->setSalary($salary);
        $this->assertTrue(true, 'Exception not happened');
    }
}
