<?php

namespace tests\unit\components;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase as Unit;

class BaseHelper extends Unit
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
