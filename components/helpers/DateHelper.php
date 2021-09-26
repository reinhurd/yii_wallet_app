<?php

namespace app\components\helpers;

class DateHelper
{
    public function getRemainingDaysOfMonth(): int
    {
        $timestamp = date('Y-m-d');
        $daysInMonth = (int)date('t', strtotime($timestamp));
        $thisDayInMonth = (int)date('j', strtotime($timestamp));

        return $daysInMonth - $thisDayInMonth;
    }
}
