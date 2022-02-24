<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Project;

use Divante\Bundle\AdventureBundle\Message\Project\CreateCriteriaProjectPair;
use Doctrine\ORM\EntityManagerInterface;

class CreateCriteriaProjectPairHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(CreateCriteriaProjectPair $message) : void
    {
        $project = $message->getProject();
        $criteria = $message->getCriteria();
        if (!$project->getCriteria()->contains($criteria)) {
            $project->addCriteria($criteria);
            $this->em->persist($project);
            $this->em->flush();
        }
    }
}
