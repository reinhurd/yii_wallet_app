<?php

namespace tests\unit\helpers;

use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\MockObject;

class BaseHelperTest extends Unit
{
    protected function createARMock(string $originalClassName): MockObject
    {
        $mock = $this->getMockBuilder($originalClassName)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->setMethodsExcept([
                '__set',
                '__get',
                '__isset',
                'tableName'
            ])
            ->getMock();

        $mock->method('hasAttribute')
            ->willReturn(true);

        return $mock;
    }
}
