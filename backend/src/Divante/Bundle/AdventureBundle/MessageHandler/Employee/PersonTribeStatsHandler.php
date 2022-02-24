<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Employee;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Repository\TribeRotationHistoryRepository;
use Divante\Bundle\AdventureBundle\Entity\TribeRotationHistory;
use Divante\Bundle\AdventureBundle\Message\Employee\PersonTribeStats;
use Doctrine\ORM\EntityManagerInterface;

class PersonTribeStatsHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param PersonTribeStats $personTribeStats
     * @throws \Exception
     */
    public function __invoke(PersonTribeStats $personTribeStats) : void
    {
        $em = $this->em;
        /** @var TribeRotationHistoryRepository $repo */
        $repo = $em->getRepository(TribeRotationHistory::class);
        $employeeRepo = $em->getRepository(Employee::class);
        /** @var Employee|null $employee */
        $employee = $employeeRepo->find($personTribeStats->getEmployeeId());
        if (is_null($employee)) {
            throw new \Exception("Employee with ID {$personTribeStats->getEmployeeId()} not found.");
        }
        /** @var TribeRotationHistory|null $record */
        $record = $repo->findOneRecordByEmployeeAndDate($employee, $personTribeStats->getData());
        if (is_null($record)) {
            $record = $this->createObjectFromTribeNameAnDate(
                $personTribeStats->getTribeName(),
                $personTribeStats->getData()
            );
        }
        $numberOfWork = $record->getNumberOfWork();
        $employees = json_decode($record->getEmployees(), true);

        if ($personTribeStats->getEmploymentStatus() === 'hired at') {
            $numberOfEnter = $record->getNumberOfEnter();
            $record->setNumberOfEnter($numberOfEnter + 1);
            $record->setNumberOfWork($numberOfWork + 1);
            if ($employee->isFemale()) {
                $numberOfFemale = $record->getNumberOfFemale();
                $record->setNumberOfFemale($numberOfFemale + 1);
            }
            if ($employee->isMale()) {
                $numberOfMale = $record->getNumberOfMale();
                $record->setNumberOfMale($numberOfMale + 1);
            }
            array_push($employees, $employee->getId());
            $record->setEmployees(json_encode($employees));
        } else {
            $numberOfLeave = $record->getNumberOfLeave();
            $record->setNumberOfLeave($numberOfLeave - 1);
            $record->setNumberOfWork($numberOfWork - 1);
            if ($employee->isFemale()) {
                $numberOfFemale = $record->getNumberOfFemale();
                $record->setNumberOfFemale($numberOfFemale - 1);
            }
            if ($employee->isMale()) {
                $numberOfMale = $record->getNumberOfMale();
                $record->setNumberOfMale($numberOfMale - 1);
            }
            $record->setEmployees(json_encode(array_diff($employees, [$employee->getId()])));
        }
        $em->persist($record);
        $em->flush();
    }

    private function createObjectFromTribeNameAnDate(string $tribeName, \DateTime $dateTime) : TribeRotationHistory
    {
        $obj = new TribeRotationHistory();
        $obj->setTribeName($tribeName);
        $obj->setYear((int)$dateTime->format('Y'));
        $obj->setMonth((int)$dateTime->format('m'));
        $obj->setCreatedAt();
        $obj->setUpdatedAt();
        $obj->setEmployees('[]');
        return $obj;
    }
}
