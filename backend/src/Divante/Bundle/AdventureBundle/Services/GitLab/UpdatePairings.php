<?php

namespace Divante\Bundle\AdventureBundle\Services\GitLab;

use Divante\Bundle\AdventureBundle\Entity\Data\IntegrationProjectEntity;
use Divante\Bundle\AdventureBundle\Entity\GitlabProject;
use Divante\Bundle\AdventureBundle\Entity\Project;
use Divante\Bundle\AdventureBundle\Message\Project\UpdateProject;
use Divante\Bundle\AdventureBundle\Services\AbstractUpdatePairings;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;

class UpdatePairings extends AbstractUpdatePairings
{
    protected function getProjectRepository(EntityManagerInterface $em): ObjectRepository
    {
        return $em->getRepository(GitlabProject::class);
    }

    /** @inheritDoc */
    protected function getCollectionFromProject(Project $project): Collection
    {
        /** @var Collection<int,IntegrationProjectEntity|GitlabProject> $collection */
        $collection = $project->getGitlabProjects();
        return $collection;
    }

    protected function clearCollection(Project $project): void
    {
        $project->clearGitlabProjects();
    }

    /** @inheritDoc */
    protected function getProjectsFromMessage(UpdateProject $message): array
    {
        return $message->getGitlabProjects();
    }
}
