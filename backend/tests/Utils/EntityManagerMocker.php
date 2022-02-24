<?php

namespace Tests\Utils;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\Matcher\AnyInvokedCount;
use PHPUnit\Framework\MockObject\MockBuilder;
use PHPUnit\Framework\MockObject\MockObject;

trait EntityManagerMocker
{
    abstract public function getMockBuilder(string $className): MockBuilder;
    abstract public static function any(): AnyInvokedCount;

    /**
     * @param array<string,ObjectRepository> $objectManagers
     * @return EntityManagerInterface
     */
    public function mockEntityManager(array $objectManagers = []) : EntityManagerInterface
    {
        /** @var EntityManagerInterface | MockObject $em */
        $em = $this->getMockBuilder(EntityManagerInterface::class)
            ->setMethods(['getRepository'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $em->expects($this->any())->method('getRepository')
            ->willReturnCallback(fn(string $name) => $objectManagers[$name] ?? null);
        return $em;
    }
}