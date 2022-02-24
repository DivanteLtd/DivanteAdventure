<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 * Date: 26.03.19
 * Time: 08:29
 */

namespace Divante\Bundle\AdventureBundle\MessageHandler\Project;

use Divante\Bundle\AdventureBundle\Entity\GitlabProject;
use Divante\Bundle\AdventureBundle\Entity\Project;
use Divante\Bundle\AdventureBundle\Message\Project\CreateProject;
use Doctrine\ORM\EntityManagerInterface;

class CreateProjectHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param CreateProject $message
     * @throws \Exception
     */
    public function __invoke(CreateProject $message) : void
    {
        $em = $this->em;

        $em->getConnection()->beginTransaction();
        try {
            $project = new Project();
            $project
                ->setName($message->getName())
                ->setCode($message->getCode())
                ->setDescription($message->getDescription())
                ->setPhoto($message->getPhoto())
                ->setUrl($message->getUrl())
                ->setType($message->getType())
                ->setBillable($message->isBillable())
                ->setPlannedBudget($message->getBudget())
                ->setStartedAt($message->getStartedAt())
                ->setEndedAt($message->getEndedAt())
                ->setCreatedAt()
                ->setUpdatedAt();
            $gitlabProjectRepo = $em->getRepository(GitlabProject::class);
            foreach ($message->getGitlabProjects() as $gitlabProjectId) {
                /** @var GitlabProject|null $gitlabProject */
                $gitlabProject = $gitlabProjectRepo->find($gitlabProjectId);
                if (!is_null($gitlabProject)) {
                    $project->addGitlabProject($gitlabProject);
                }
            }
            $em->persist($project);
            $em->flush();
            $em->getConnection()->commit();
        } catch (\Exception $e) {
            $em->getConnection()->rollBack();
            throw $e;
        }
    }
}
