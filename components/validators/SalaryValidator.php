<?php

namespace app\components\validators;

class SalaryValidator
{
    private const FUNDS_SUM = 1.0;

    /**
     * @param mixed[] $fundsSalaryWeights
     */
    public function validateSalaryFundsSum(array $fundsSalaryWeights): bool
    {
        $result = 0.0;
        foreach ($fundsSalaryWeights as $name => $value) {
            if (!is_numeric($value)) {
                return false;
            }
            $result += $value;
        }
        if ($result !== self::FUNDS_SUM) {
            return false;
        }

        return true;
    }
}
