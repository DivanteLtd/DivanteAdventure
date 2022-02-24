<?php
namespace Divante\Bundle\AdventureBundle\Message\Evidence;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Doctrine\Common\Persistence\ObjectManager;

trait OvertimeTrait
{
    /** @var array<int,array<string,mixed>> */
    private array $overtimeEntries;
    private int $managerId;

    /** @return array<int,array<string,mixed>> */
    public function getOvertimeEntries() : array
    {
        return $this->overtimeEntries;
    }

    public function tryFindManager(ObjectManager $objectManager) : ?Employee
    {
        $repository = $objectManager->getRepository(Employee::class);
        /** @var Employee|null $manager */
        $manager = $repository->find($this->managerId);
        return $manager;
    }
}
