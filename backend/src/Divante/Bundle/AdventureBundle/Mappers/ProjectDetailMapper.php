<?php
/**
 * Created by PhpStorm.
 * User: norbert
 * Date: 21.12.18
 * Time: 08:55
 */

namespace Divante\Bundle\AdventureBundle\Mappers;

use Divante\Bundle\AdventureBundle\Entity\DataProcessingCriteria;
use Divante\Bundle\AdventureBundle\Entity\EmployeeProject;
use Divante\Bundle\AdventureBundle\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;

class ProjectDetailMapper extends AbstractDoctrineAwareMapper implements Mapper
{
    private GitlabProjectMapper $gitlabProjectMapper;

    public function __construct(
        EntityManagerInterface $manager,
        GitlabProjectMapper $gitlabProjectMapper
    ) {
        parent::__construct($manager);
        $this->gitlabProjectMapper = $gitlabProjectMapper;
    }

    /**
     * @param Project $project
     * @return array<string,mixed>
     */
    public function __invoke(Project $project) : array
    {
        return $this->mapEntity($project);
    }

    /**
     * @param Project $entity
     * @return array<string,mixed>
     */
    public function mapEntity($entity): array
    {
        return [
            "id" => $entity->getId(),
            "name" => $entity->getName(),
            "description" => $entity->getDescription() ?? "",
            "photo" => $entity->getPhoto() ?? "",
            "url" => $entity->getUrl() ?? "",
            "started_at" => $entity->getStartedAtTimestamp(),
            "ended_at" => $entity->getEndedAtTimestamp(),
            "project_type" => $entity->getType(),
            "sum_type" => $entity->getSumType(),
            "planned_budget" => $entity->getPlannedBudget() ?? -1,
            "tribes" => $this->getTribes($entity),
            "criteria" => $this->getCriteria($entity),
            "visibility" => !$entity->isArchived(),
            "billable" => $entity->isBillable(),
            "code" => $entity->getCode(),
            "gitlab_projects" => $this->getGitlabProjects($entity),
            'connectedToSlack' => $entity->getSlackStatus() === Project::SLACK_AUTHORIZED
        ];
    }

    /**
     * @param Project $entity
     * @return array<int,array<string,mixed>>
     */
    private function getCriteria(Project $entity) : array
    {
        $em = $this->getObjectManager();
        $allProjectsCriteria = $em->getRepository(DataProcessingCriteria::class)->findAll();
        $criteria = [];
        /** @var DataProcessingCriteria $projectCriteria */
        foreach ($allProjectsCriteria as $projectCriteria) {
            $projects = $projectCriteria->getProjects()->filter(
                function (Project $project) use ($entity) {
                    return $project->getId() === $entity->getId();
                }
            );
            if (!$projects->isEmpty()) {
                array_push($criteria, [
                    'id' => $projectCriteria->getId(),
                    'namePl' => $projectCriteria->getNamePl(),
                    'nameEn' => $projectCriteria->getNameEn()
                ]);
            }
        }
        return $criteria;
    }

    /**
     * @param Project $project
     * @return array<int,array<string,mixed>>
     */
    private function getTribes(Project $project) : array
    {
        $em = $this->getObjectManager();
        $employeeProjects = $em->getRepository(EmployeeProject::class)
            ->findBy([
                'project' => $project->getId()
            ]);
        $tribes = [];
        foreach ($employeeProjects as $employeeProject) {
            $tribe = $employeeProject->getEmployee()->getTribe();
            $tribeId = is_null($tribe) ? null : $tribe->getId();
            if (!is_null($tribeId)) {
                array_push($tribes, $tribeId);
            }
        }
        $uniqueTribes = array_unique($tribes);
        $tribes = [];
        foreach ($uniqueTribes as $uniqueTribe) {
            array_push($tribes, $uniqueTribe);
        }
        return $tribes;
    }

    /**
     * @param Project $project
     * @return array<int,array<string,mixed>>
     */
    private function getGitlabProjects(Project $project) : array
    {
        return array_map($this->gitlabProjectMapper, $project->getGitlabProjects()->toArray());
    }
}
