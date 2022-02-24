<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 * Date: 13.02.19
 * Time: 11:57
 */

namespace Divante\Bundle\AdventureBundle\MessageHandler\Project;

use Divante\Bundle\AdventureBundle\Entity\EmployeeProject;
use Divante\Bundle\AdventureBundle\Events\Integration\AbstractEmployeeProjectAccessEvent;
use Divante\Bundle\AdventureBundle\Events\ProjectOccupancyEvent;
use Divante\Bundle\AdventureBundle\Message\Project\HideProject;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class HideProjectHandler
{
    private EntityManagerInterface $em;
    private EventDispatcherInterface $dispatcher;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $dispatcher)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param HideProject $message
     * @throws \Exception
     */
    public function __invoke(HideProject $message) : void
    {
        $em = $this->em;
        $project = $message->getEntry();
        $em->getConnection()->beginTransaction();
        try {
            $project
                ->setArchived($message->isArchived())
                ->setEndedAt((new \DateTime())->format('Y-m-d'))
                ->setUpdatedAt();
            $em->persist($project);
            $em->flush();
            $em->getConnection()->commit();

            $repo = $em->getRepository(EmployeeProject::class);
            /** @var EmployeeProject[] $pairings */
            $pairings = $repo->findBy([
                'project' => $project
            ]);
            /** @var EmployeeProject $pairing */
            foreach ($pairings as $pairing) {
                $events = AbstractEmployeeProjectAccessEvent::createEvents($pairing->getEmployee(), $project, false);
                foreach ($events as $event) {
                    $this->dispatcher->dispatch($event);
                }
            }
            $event = new ProjectOccupancyEvent($project);
            $this->dispatcher->dispatch($event);
        } catch (\Exception $e) {
            $em->getConnection()->rollBack();
            throw new \Exception("Updating project failed", 0, $e);
        }
    }
}
