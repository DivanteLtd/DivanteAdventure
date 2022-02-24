<?php


namespace Divante\Bundle\AdventureBundle\Services\Decorators;

use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractDecorator
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    abstract public function decorate(?int $id): string;

    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }
}
