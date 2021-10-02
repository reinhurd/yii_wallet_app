<?php

namespace tests\unit\components;

use app\components\validators\SalaryValidator;
use Codeception\Test\Unit;

class SalaryValidatorTest extends Unit
{
    private $salaryValidator;

    public function setUp(): void
    {
        parent::setUp();
        $this->salaryValidator = new SalaryValidator();
    }

    /**
     * @dataProvider validateSalaryFundsDataProvider
     */
    public function testValidateSalaryFundsSum(
        array $fundsSum,
        bool $expectedResult
    ): void {
        $result= $this->salaryValidator->validateSalaryFundsSum($fundsSum);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return mixed[]
     */
    public function validateSalaryFundsDataProvider(): array
    {
        return [
            [
                [
                    'a' => 0.2,
                    'b' => 0.9
                ],
                false
            ],
            [
                [
                    'a' => 0.4,
                    'b' => 0.6
                ],
                true
            ]
        ];
    }
}