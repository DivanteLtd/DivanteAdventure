<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistTemplate;
use Divante\Bundle\AdventureBundle\Message\Checklist\DeleteChecklistTemplate;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeleteChecklistTemplateHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(DeleteChecklistTemplate $message) : void
    {
        $repo = $this->em->getRepository(ChecklistTemplate::class);
        /** @var ChecklistTemplate|null $checklist */
        $checklist = $repo->find($message->getChecklistId());
        if (is_null($checklist)) {
            throw new NotFoundHttpException("Checklist with given ID not found");
        }
        $this->em->remove($checklist);
    }
}
