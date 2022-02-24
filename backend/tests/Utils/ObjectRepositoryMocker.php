<?php

namespace Tests\Utils;

use Doctrine\Common\Persistence\ObjectRepository;
use PHPUnit\Framework\MockObject\Matcher\AnyInvokedCount;
use PHPUnit\Framework\MockObject\MockBuilder;
use PHPUnit\Framework\MockObject\MockObject;

trait ObjectRepositoryMocker
{
    abstract public function getMockBuilder(string $className): MockBuilder;
    abstract public static function any(): AnyInvokedCount;

    public function mockObjectRepository(?callable $findCallback = null) : ObjectRepository
    {
        $findCallback ??= fn() => null;
        /** @var ObjectRepository|MockObject $questionRepo */
        $questionRepo = $this->getMockBuilder(ObjectRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['find'])
            ->getMockForAbstractClass();
        $questionRepo->expects($this->any())->method('find')->willReturnCallback($findCallback);
        return $questionRepo;
    }
}
