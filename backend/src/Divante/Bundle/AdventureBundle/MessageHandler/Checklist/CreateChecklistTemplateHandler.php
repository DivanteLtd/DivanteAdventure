<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistTemplate;
use Divante\Bundle\AdventureBundle\Message\Checklist\CreateChecklistTemplate;
use Doctrine\ORM\EntityManagerInterface;

class CreateChecklistTemplateHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(CreateChecklistTemplate $message) : void
    {
        $template = new ChecklistTemplate();
        $template
            ->setType($message->getType())
            ->setNameEn($message->getNameEn())
            ->setNamePl($message->getNamePl())
            ->setCreatedAt()
            ->setUpdatedAt();
        $this->em->persist($template);
    }
}
