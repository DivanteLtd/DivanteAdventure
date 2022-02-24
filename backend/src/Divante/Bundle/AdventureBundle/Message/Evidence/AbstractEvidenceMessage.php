<?php
namespace Divante\Bundle\AdventureBundle\Message\Evidence;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Evidence;
use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;
use Doctrine\Common\Persistence\ObjectManager;

abstract class AbstractEvidenceMessage
{
    use ObjectTrait;

    private Employee $employee;
    private int $year;
    private int $month;

    public function __construct(Employee $employee, int $year, int $month)
    {
        $this->employee = $employee;
        $this->year = $year;
        $this->month = $month;
    }

    abstract public function getSuccessfulResultMessage() : string;

    public function tryFindExistingEvidence(ObjectManager $manager) : ?Evidence
    {
        $repository = $manager->getRepository(Evidence::class);
        /** @var Evidence|null $evidence */
        $evidence = $repository->findOneBy([
            'employee' => $this->employee,
            'year' => $this->year,
            'month' => $this->month
        ]);
        return $evidence;
    }

    public function getEmployee() : Employee
    {
        return $this->employee;
    }

    public function getYear() : int
    {
        return $this->year;
    }

    public function getMonth() : int
    {
        return $this->month;
    }

    abstract public function shouldSendNotification() : bool;
}
