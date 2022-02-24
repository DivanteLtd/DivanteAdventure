<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistTemplateQuestion;
use Divante\Bundle\AdventureBundle\Message\Checklist\DeleteChecklistTemplateQuestion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeleteChecklistTemplateQuestionHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(DeleteChecklistTemplateQuestion $message) : void
    {
        $question = $this->findQuestion($message);
        $this->em->remove($question);
    }

    private function findQuestion(DeleteChecklistTemplateQuestion $message) : ChecklistTemplateQuestion
    {
        $repo = $this->em->getRepository(ChecklistTemplateQuestion::class);
        /** @var ChecklistTemplateQuestion|null $question */
        $question = $repo->find($message->getQuestionId());
        if (is_null($question)) {
            throw new NotFoundHttpException("Checklist question with given ID has not been found");
        }
        return $question;
    }
}
