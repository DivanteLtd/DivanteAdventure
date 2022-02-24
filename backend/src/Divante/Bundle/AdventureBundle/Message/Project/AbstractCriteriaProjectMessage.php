<?php

namespace Divante\Bundle\AdventureBundle\Message\Project;

use Divante\Bundle\AdventureBundle\Entity\DataProcessingCriteria;
use Divante\Bundle\AdventureBundle\Entity\Project;
use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class AbstractCriteriaProjectMessage
{
    use ObjectTrait;

    private Project $project;
    private DataProcessingCriteria $criteria;

    /**
     * CreateCriteriaProjectPair constructor.
     * @param int $projectId
     * @param int $criteriaId
     * @param ObjectManager $entityManager
     * @throws NotFoundHttpException, BadRequestHttpException
     */
    public function __construct(int $projectId, int $criteriaId, ObjectManager $entityManager)
    {
        /** @var Project|null $project */
        $project = $entityManager
            ->getRepository(Project::class)
            ->find($projectId);
        /** @var DataProcessingCriteria|null $criteria */
        $criteria = $entityManager
            ->getRepository(DataProcessingCriteria::class)
            ->find($criteriaId);
        if (is_null($project)) {
            throw new NotFoundHttpException(sprintf("Project with ID %d not found", $projectId));
        }
        if (is_null($criteria)) {
            throw new NotFoundHttpException(sprintf("Criteria with ID %d not found", $criteriaId));
        }
        $this->project = $project;
        $this->criteria = $criteria;
    }

    public function getProject() : Project
    {
        return $this->project;
    }

    public function getCriteria() : DataProcessingCriteria
    {
        return $this->criteria;
    }
}
