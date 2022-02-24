<?php
/**
 * Created by PhpStorm.
 * User: pawel
 * Date: 01.02.19
 * Time: 13:46
 */

namespace Divante\Bundle\AdventureBundle\MessageHandler\Project;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\EmployeeOccupancy;
use Divante\Bundle\AdventureBundle\Entity\EmployeeProject;
use Divante\Bundle\AdventureBundle\Entity\Project;
use Divante\Bundle\AdventureBundle\Events\Integration\AbstractEmployeeProjectAccessEvent;
use Divante\Bundle\AdventureBundle\Message\Project\UpdatePairing;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;

class UpdatePairingHandler
{
    private EntityManagerInterface $em;
    private EventDispatcherInterface $dispatcher;
    
    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $dispatcher)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param UpdatePairing $message
     * @throws \Exception
     */
    public function __invoke(UpdatePairing $message) : void
    {
        $id = $message->getId();
        /** @var EmployeeProject|null $pairing */
        $pairing = $this->em->getRepository(EmployeeProject::class)->find($id);
        if (is_null($pairing)) {
            throw new \Exception("Pairing with ID $id not found", Response::HTTP_NOT_FOUND);
        }
        $employeeId = $message->getEmployeeId();
        $projectId = $message->getProjectId();
        $occupancyEntries = $this->em->getRepository(EmployeeOccupancy::class)->findBy(
            [
                'employee' => $employeeId,
                'project' => $projectId
            ]
        );
        $datesFrom = $message->getDatesFrom();
        $datesTo = $message->getDatesTo();
        $correctOccupancyEntriesId = [];
        $allOccupancyEntriesId = [];
        /** @var EmployeeOccupancy $occupancyEntry */
        foreach ($occupancyEntries as $occupancyEntry) {
            foreach ($datesFrom as $key => $dateFrom) {
                $startDate = \DateTime::createFromFormat('m-Y', $dateFrom)->format('Y-m') . '-01';
                $endDate = \DateTime::createFromFormat('m-Y', $datesTo[$key])->format('Y-m-t');
                $start_ts = strtotime($startDate);
                $end_ts = strtotime($endDate);
                if (($occupancyEntry->getDate() >= $start_ts) && ($occupancyEntry->getDate() <= $end_ts)) {
                    array_push($correctOccupancyEntriesId, $occupancyEntry->getId());
                }
            }
            array_push($allOccupancyEntriesId, $occupancyEntry->getId());
        }
        $occupancyEntriesIdToDelete = array_diff_assoc($allOccupancyEntriesId, $correctOccupancyEntriesId);
        $occupancyEntriesToDelete = [];
        $occupancyRepo = $this->em->getRepository(EmployeeOccupancy::class);
        foreach ($occupancyEntriesIdToDelete as $occupancyEntryIdToDelete) {
            $occupancyObject = $occupancyRepo->find($occupancyEntryIdToDelete);
            array_push($occupancyEntriesToDelete, $occupancyObject);
        }

        if (count($datesFrom) === 0) {
            $pairing->setOvertime($message->getOvertime());
        } else {
            $pairing
            ->setDateFrom($datesFrom)
            ->setDateTo($datesTo);
        }
        $addAccess = false;
        for ($i = 0; $i < min(count($datesFrom), count($datesTo)); $i++) {
            $startDate = \DateTime::createFromFormat('m-Y', $datesFrom[$i])->format('Y-m') . '-01';
            $endDate = \DateTime::createFromFormat('m-Y', $datesTo[$i])->format('Y-m-t');
            $start_ts = strtotime($startDate);
            $end_ts = strtotime($endDate);
            $currentTime = time();
            if ($start_ts <= $currentTime && $end_ts >= $currentTime) {
                $addAccess = true;
                break;
            }
        }
        $this->refreshAccess($pairing->getEmployee(), $pairing->getProject(), $addAccess);
        try {
            $this->em->persist($pairing);
            if (count($datesFrom) > 0) {
                foreach ($occupancyEntriesToDelete as $entryToDelete) {
                    $this->em->remove($entryToDelete);
                }
            }
            $this->em->flush();
        } catch (\Exception $exception) {
            throw new \Exception("Cant't saved pair entity");
        }
    }

    private function refreshAccess(Employee $employee, Project $project, bool $addAccess) : void
    {
        $events = AbstractEmployeeProjectAccessEvent::createEvents($employee, $project, $addAccess);
        foreach ($events as $event) {
            $this->dispatcher->dispatch($event);
        }
    }
}
