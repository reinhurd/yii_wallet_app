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

    public function testValidateSalaryFundsSum(): void
    {
        $testArrayInvalid = [
            'a' => 0.2,
            'b' => 0.9
        ];

        $testArrayValid = [
            'a' => 0.4,
            'b' => 0.6
        ];

        $resultInvalid = $this->salaryValidator->validateSalaryFundsSum($testArrayInvalid);
        $resultValid = $this->salaryValidator->validateSalaryFundsSum($testArrayValid);

        $this->assertFalse($resultInvalid);
        $this->assertTrue($resultValid);
    }
}