<?php

namespace Divante\Bundle\AdventureBundle\EventSubscriber\Integration;

use Divante\Bundle\AdventureBundle\Entity\IntegrationQueue;
use Divante\Bundle\AdventureBundle\Events\Integration\AbstractEmployeeProjectAccessEvent;
use Divante\Bundle\AdventureBundle\Events\Integration\AbstractGitlabAccessEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class AbstractIntegrationSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function handleEntry(AbstractEmployeeProjectAccessEvent $event, string $type): void
    {
        $queue = new IntegrationQueue();
        $queue
            ->setStatus(IntegrationQueue::STATUS_ENQUEUED)
            ->setType($type)
            ->setEmployee($event->getEmployee())
            ->setProject($event->getProject())
            ->setRequestData($this->createRequestData($event))
            ->setCreatedAt()
            ->setUpdatedAt();
        $this->entityManager->persist($queue);
        $this->entityManager->flush();
    }

    /**
     * @param AbstractEmployeeProjectAccessEvent $event
     * @return array<string,int>
     */
    private function createRequestData(AbstractEmployeeProjectAccessEvent $event) : array
    {
        if ($event instanceof AbstractGitlabAccessEvent) {
            return [ 'gitlab_project_id' => $event->getGitlabProject()->getId() ];
        } else {
            return [];
        }
    }
}
