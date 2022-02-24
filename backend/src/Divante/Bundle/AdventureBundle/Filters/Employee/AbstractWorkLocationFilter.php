<?php

namespace Divante\Bundle\AdventureBundle\Filters\Employee;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\EmployeeWorkLocation;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractWorkLocationFilter
{
    private EntityManagerInterface $entityManager;
    /** @var array<int,array<int,bool>> */
    private static array $cache = [];

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(Employee $employee) : bool
    {
        if (!is_null($this->getCache($employee->getId()))) {
            return $this->getCache($employee->getId());
        }
        $today = (new \DateTime())->format('Y-m-d');
        $repo = $this->entityManager->getRepository(EmployeeWorkLocation::class);
        $locations = $repo->findBy([
            'employeeId' => $employee->getId(),
            'type' => $this->getType(),
        ]);
        foreach ($locations as $locationDate) {
            if ($locationDate->getDate()->format('Y-m-d') === $today) {
                self::setCache($employee->getId(), true);
                return true;
            }
        }
        self::setCache($employee->getId(), false);
        return false;
    }

    private function getCache(int $employeeId) : ?bool
    {
        $subarray = self::$cache[$this->getType()] ?? [];
        if (array_key_exists($employeeId, $subarray)) {
            return $subarray[$employeeId];
        }
        return null;
    }

    private function setCache(int $employeeId, bool $value) : void
    {
        if (!array_key_exists($this->getType(), self::$cache)) {
            self::$cache[$this->getType()] = [];
        }
        self::$cache[$this->getType()][$employeeId] = $value;
    }

    abstract protected function getType() : int;
}
