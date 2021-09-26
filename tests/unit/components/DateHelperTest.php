<?php

namespace tests\unit\components;

use app\components\helpers\DateHelper;
use Codeception\Test\Unit;

class DateHelperTest extends Unit
{
    private $helper;

    public function setUp(): void
    {
        parent::setUp();
        $this->helper = new DateHelper();
    }

    public function testGetRemainingDaysOfMonth(): void
    {
        $timestamp = date('Y-m-d');
        $daysInMonth = (int)date('t', strtotime($timestamp));
        $thisDayInMonth = (int)date('j', strtotime($timestamp));

        $expected = $daysInMonth - $thisDayInMonth;
        $result = $this->helper->getRemainingDaysOfMonth();

        $this->assertEquals($expected, $result);
    }
}
