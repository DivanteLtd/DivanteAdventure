<?php

namespace Divante\Bundle\AdventureBundle\EventSubscriber\Integration;

use Divante\Bundle\AdventureBundle\Entity\IntegrationQueue;
use Divante\Bundle\AdventureBundle\Events\Integration\AddGitlabAccessEvent;
use Divante\Bundle\AdventureBundle\Events\Integration\RemoveGitlabAccessEvent;

class GitlabIntegrationSubscriber extends AbstractIntegrationSubscriber
{
    /** @return array<string,array<int,array<int,string|int>>> */
    public static function getSubscribedEvents(): array
    {
        return [
            AddGitlabAccessEvent::class => [
                [ 'addAccess', 0 ],
            ],
            RemoveGitlabAccessEvent::class => [
                [ 'removeAccess', 0 ],
            ],
        ];
    }

    public function addAccess(AddGitlabAccessEvent $event): void
    {
        $this->handleEntry($event, IntegrationQueue::TYPE_GITLAB_ADD);
    }

    public function removeAccess(RemoveGitlabAccessEvent $event): void
    {
        $this->handleEntry($event, IntegrationQueue::TYPE_GITLAB_REMOVE);
    }
}
