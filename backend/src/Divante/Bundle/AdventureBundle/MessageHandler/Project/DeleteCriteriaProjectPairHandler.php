<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Project;

use Divante\Bundle\AdventureBundle\Message\Project\DeleteCriteriaProjectPair;
use Doctrine\ORM\EntityManagerInterface;

class DeleteCriteriaProjectPairHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(DeleteCriteriaProjectPair $message) : void
    {
        $project = $message->getProject();
        $criteria = $message->getCriteria();
        $project->getCriteria()->removeElement($criteria);
        $this->em->persist($project);
        $this->em->flush();
    }
}
