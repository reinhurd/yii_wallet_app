<?php

namespace tests\unit\components;

use app\components\helpers\DescriptionHelper;
use Codeception\Test\Unit;

class DescriptionHelperTest extends Unit
{
    private $helper;

    public function setUp(): void
    {
        parent::setUp();
        $this->helper = \Yii::createObject(DescriptionHelper::class);
    }

    public function testGetDescriptionFromArray(): void
    {
        $array = [1=>'one', 2=>'two'];
        $delimeter = ' => ';
        $expected = urlencode("\n" . 1 . $delimeter . 'one' . "\n" . 2 . $delimeter . 'two');
        $result = $this->helper->getDescriptionFromArray($array);

        $this->assertEquals($expected, $result);
    }
}