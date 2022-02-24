<?php

namespace Divante\Bundle\AdventureBundle\EventSubscriber\Tribe;

use DateTime;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Tribe;
use Divante\Bundle\AdventureBundle\Entity\TribeRotationHistory;
use Divante\Bundle\AdventureBundle\Events\Tribe\TribeAssignmentChangeEvent;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TribeChangedSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /** @return array<string,array<int,array<int,string|int>>> */
    public static function getSubscribedEvents() : array
    {
        return [
            TribeAssignmentChangeEvent::class => [
                [ 'onTribeChange', 0 ]
            ],
        ];
    }

    /**
     * @param TribeAssignmentChangeEvent $event
     * @throws Exception
     */
    public function onTribeChange(TribeAssignmentChangeEvent $event) : void
    {
        /** @var Tribe|null $oldTribe */
        $oldTribe = null;
        /** @var Tribe|null $newTribe */
        $newTribe = null;
        $date = $event->getDate();
        /** @var Employee|null $employee */
        $employee = $this->entityManager->getRepository(Employee::class)
            ->find($event->getEmployeeId());
        if (!is_null($event->getOldTribeId())) {
            /** @var Tribe|null $oldTribe */
            $oldTribe = $this->entityManager->getRepository(Tribe::class)
                ->find($event->getOldTribeId());
        }
        if (!is_null($event->getNewTribeId())) {
            /** @var Tribe|null $newTribe */
            $newTribe = $this->entityManager->getRepository(Tribe::class)
                ->find($event->getNewTribeId());
        }
        if (!is_null($oldTribe)) {
            $history = $this->getHistoryEntityByDataOrCreatedIfNeeded($oldTribe, $date);

            $history->setNumberOfLeave(
                $history->getNumberOfLeave() + 1
            );
            $history->setNumberOfWork(
                $this->odd($history->getNumberOfWork())
            );
            if ($employee->isMale()) {
                $history->setNumberOfMale(
                    $this->odd($history->getNumberOfMale())
                );
            }
            if ($employee->isFemale()) {
                $history->setNumberOfFemale(
                    $this->odd($history->getNumberOfFemale())
                );
            }
            $history->setEmployees(
                $this->addEmployee($history->getEmployees(), $employee->getId())
            );
            $history->setUpdatedAt();
            $this->entityManager->persist($history);
            $this->entityManager->flush();
        }
        if (!is_null($newTribe)) {
            $history = $this->getHistoryEntityByDataOrCreatedIfNeeded($newTribe, $date);
            $history->setNumberOfEnter(
                $history->getNumberOfEnter() + 1
            );
            $history->setNumberOfWork(
                $history->getNumberOfWork() + 1
            );
            if ($employee->isFemale()) {
                $history->setNumberOfFemale(
                    $history->getNumberOfFemale() + 1
                );
            }
            if ($employee->isMale()) {
                $history->setNumberOfMale(
                    $history->getNumberOfMale() + 1
                );
            }
            $history->setEmployees(
                $this->addEmployee(
                    $history->getEmployees(),
                    $employee->getId()
                )
            );
            $history->setUpdatedAt();
            $this->entityManager->persist($history);
            $this->entityManager->flush();
        }
    }

    /**
     * @param Tribe $tribe
     * @param DateTime $dateTime
     * @return TribeRotationHistory
     * @throws Exception
     */
    private function getHistoryEntityByDataOrCreatedIfNeeded(Tribe $tribe, DateTime $dateTime) :TribeRotationHistory
    {
        /** @var TribeRotationHistory|null $history */
        $history = $this->entityManager->getRepository(TribeRotationHistory::class)
            ->findOneBy([
                'tribeName' => $tribe->getName(),
                'year' => $dateTime->format('Y'),
                'month' => $dateTime->format('m')
            ]);
        if (is_null($history)) {
            $history = new TribeRotationHistory();
            $history->setTribeName($tribe->getName());
            $history->setYear((int)$dateTime->format('Y'));
            $history->setMonth((int)$dateTime->format('m'));
            $history->setEmployees('[]');
            $history->setCreatedAt();
        }
        return $history;
    }

    private function odd(?int $integer) : int
    {
        $result = intval($integer) - 1;
        if ($result < 0) {
            return 0;
        }
        return $result;
    }

    private function addEmployee(string $json, int $employeeId) : string
    {
        $employees = json_decode($json, true);
        array_push($employees, $employeeId);
        $encoded = json_encode($employees);
        return is_string($encoded) ? $encoded : '{}';
    }
}
