<?php

namespace Divante\Bundle\AdventureBundle\Services;

use Divante\Bundle\AdventureBundle\Entity\Data\IntegrationProjectEntity;
use Divante\Bundle\AdventureBundle\Entity\EmployeeProject;
use Divante\Bundle\AdventureBundle\Entity\GitlabProject;
use Divante\Bundle\AdventureBundle\Entity\Project;
use Divante\Bundle\AdventureBundle\Events\Integration\AddGitlabAccessEvent;
use Divante\Bundle\AdventureBundle\Events\Integration\RemoveGitlabAccessEvent;
use Divante\Bundle\AdventureBundle\Filters\EmployeeProject\EmployeeProjectActive;
use Divante\Bundle\AdventureBundle\Message\Project\UpdateProject;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractUpdatePairings
{
    private EntityManagerInterface $em;
    private EventDispatcherInterface $eventDispatcher;
    private EmployeeProjectActive $filter;

    public function __construct(
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher,
        EmployeeProjectActive $filter
    ) {
        $this->em = $em;
        $this->eventDispatcher = $dispatcher;
        $this->filter = $filter;
    }

    /**
     * @param Project $project
     * @param UpdateProject $message
     * @throws \Exception
     */
    final public function update(Project $project, UpdateProject $message): void
    {
        /** @var int[] $existingProjects */
        $existingProjects = $this->getCollectionFromProject($project)->map(
            function (IntegrationProjectEntity $projectEntity) {
                return $projectEntity->getId();
            }
        )->toArray();
        $addIds = [];
        $removeIds = [];
        $this->clearCollection($project);

        /** @var array<string,mixed>|int $messageEntry */
        foreach ($this->getProjectsFromMessage($message) as $messageEntry) {
            $projectId = is_int($messageEntry) ? $messageEntry : $messageEntry['id'];
            /** @var IntegrationProjectEntity|null $projectEntity */
            $projectEntity = $this->getProjectRepository($this->em)->find($projectId);
            if (!is_null($projectEntity)) {
                $this->addToCollection($project, $projectEntity);
                if (in_array($projectId, $existingProjects)) {
                    $index = array_search($projectId, $existingProjects);
                    unset($existingProjects[$index]);
                } else {
                    $addIds[] = $projectId;
                }
            }
        }
        foreach ($existingProjects as $projectId) {
            $removeIds[] = $projectId;
        }
        $this->changeAccess($project, $addIds, $removeIds);
    }

    /**
     * @param Project $project
     * @param int[] $addIds
     * @param int[] $removeIds
     * @throws \Exception
     */
    private function changeAccess(Project $project, array $addIds, array $removeIds) : void
    {
        $projectRepo = $this->getProjectRepository($this->em);
        /** @var IntegrationProjectEntity[] $addProjects */
        $addProjects = $projectRepo->findBy([
            'id' => $addIds
        ]);
        /** @var IntegrationProjectEntity[] $removeProjects */
        $removeProjects = $projectRepo->findBy([
            'id' => $removeIds
        ]);

        $pairingsRepo = $this->em->getRepository(EmployeeProject::class);
        /** @var EmployeeProject[] $pairings */
        $pairings = $pairingsRepo->findBy([
            'project' => $project
        ]);
        /** @var EmployeeProject[] $activePairings */
        $activePairings = array_filter($pairings, $this->filter);
        foreach ($activePairings as $pairing) {
            $this->dispatchEvents($pairing, $addProjects, $removeProjects);
        }
    }

    /**
     * @param EmployeeProject $pairing
     * @param IntegrationProjectEntity[] $addEntities
     * @param IntegrationProjectEntity[] $removeEntities
     * @throws \Exception
     */
    private function dispatchEvents(EmployeeProject $pairing, array $addEntities, array $removeEntities) : void
    {
        foreach ($addEntities as $addEntity) {
            if ($addEntity instanceof GitlabProject) {
                $this->eventDispatcher->dispatch(
                    new AddGitlabAccessEvent($pairing->getEmployee(), $pairing->getProject(), $addEntity)
                );
            }
        }
        foreach ($removeEntities as $removeEntity) {
            if ($removeEntity instanceof GitlabProject) {
                $this->eventDispatcher->dispatch(
                    new RemoveGitlabAccessEvent($pairing->getEmployee(), $pairing->getProject(), $removeEntity)
                );
            }
        }
    }

    /**
     * @param Project $project
     * @param IntegrationProjectEntity $entity
     * @throws \Exception
     */
    private function addToCollection(Project $project, IntegrationProjectEntity $entity): void
    {
        if ($entity instanceof GitlabProject) {
            $project->addGitlabProject($entity);
        } else {
            throw new \Exception("Unrecognized type of integration");
        }
    }

    abstract protected function getProjectRepository(EntityManagerInterface $em) : ObjectRepository;

    /**
     * @param Project $project
     * @return Collection<int,IntegrationProjectEntity>
     */
    abstract protected function getCollectionFromProject(Project $project) : Collection;
    abstract protected function clearCollection(Project $project) : void;

    /**
     * @param UpdateProject $message
     * @return array<int,int|array<string|int,int|mixed>>
     */
    abstract protected function getProjectsFromMessage(UpdateProject $message) : array;
}
