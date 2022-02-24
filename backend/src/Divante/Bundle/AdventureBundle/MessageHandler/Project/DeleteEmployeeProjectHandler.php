<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 * Date: 13.03.19
 * Time: 14:44
 */

namespace Divante\Bundle\AdventureBundle\MessageHandler\Project;

use Divante\Bundle\AdventureBundle\Entity\EmployeeOccupancy;
use Divante\Bundle\AdventureBundle\Entity\EmployeeProject;
use Divante\Bundle\AdventureBundle\Events\Integration\AbstractEmployeeProjectAccessEvent;
use Divante\Bundle\AdventureBundle\Events\ProjectUnassignEvent;
use Divante\Bundle\AdventureBundle\Message\Project\DeleteEmployeeProject;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DeleteEmployeeProjectHandler
{
    private EntityManagerInterface $em;
    private EventDispatcherInterface $dispatcher;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $dispatcher)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param DeleteEmployeeProject $message
     * @throws \Exception
     */
    public function __invoke(DeleteEmployeeProject $message) : void
    {
        $em = $this->em;
        $id = $message->getEntryId();
        /** @var EmployeeProject|null $entry */
        $entry = $em->getRepository(EmployeeProject::class)->find($id);
        if (is_null($entry)) {
            return; // already deleted
        }
        $employeeId = $message->getEmployeeId();
        $projectId = $message->getProjectId();
        $occupancyEntries = $em->getRepository(EmployeeOccupancy::class)->findBy(
            [
                'employee' => $employeeId,
                'project' => $projectId
            ]
        );
        foreach ($occupancyEntries as $occupancyEntry) {
            $em->remove($occupancyEntry);
        }
        $entry
            ->setDateFrom([])
            ->setDateTo([]);
        $em->flush();
        $this->dispatcher->dispatch(new ProjectUnassignEvent($entry->getEmployee(), $entry->getProject()));
        $events = AbstractEmployeeProjectAccessEvent::createEvents($entry->getEmployee(), $entry->getProject(), false);
        foreach ($events as $event) {
            $this->dispatcher->dispatch($event);
        }
    }
}
