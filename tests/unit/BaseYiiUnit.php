<?php

namespace tests\unit;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase as Unit;

class BaseYiiUnit extends Unit
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
