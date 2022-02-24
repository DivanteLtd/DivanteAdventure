<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistTemplate;
use Divante\Bundle\AdventureBundle\Message\Checklist\EditChecklistTemplate;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EditChecklistTemplateHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(EditChecklistTemplate $message) : void
    {
        $repo = $this->em->getRepository(ChecklistTemplate::class);
        /** @var ChecklistTemplate|null $checklist */
        $checklist = $repo->find($message->getId());
        if (is_null($checklist)) {
            throw new NotFoundHttpException("Checklist with given ID not found");
        }
        $checklist->setUpdatedAt();

        if (!is_null($message->getNamePl())) {
            $checklist->setNamePl($message->getNamePl());
        }
        if (!is_null($message->getNameEn())) {
            $checklist->setNameEn($message->getNameEn());
        }
    }
}
