<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Checklist;

use DateTime;
use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistQuestion;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Filters\Checklist\TaskDoneFilter;
use Divante\Bundle\AdventureBundle\Message\Checklist\PingChecklistQuestion;
use Divante\Bundle\AdventureBundle\Services\ChecklistQuestionPinger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PingChecklistQuestionHandler
{
    private EntityManagerInterface $em;
    private TaskDoneFilter $filter;
    private ChecklistQuestionPinger $pinger;

    public function __construct(EntityManagerInterface $em, TaskDoneFilter $filter, ChecklistQuestionPinger $pinger)
    {
        $this->em = $em;
        $this->filter = $filter;
        $this->pinger = $pinger;
    }

    public function __invoke(PingChecklistQuestion $message) : void
    {
        $question = $this->getQuestion($message);
        $responsible = $this->getPinged($question);
        $this->pinger->ping($responsible, $message->getUser(), $question);
        $question->setLastPingDate(new DateTime());
    }

    private function getQuestion(PingChecklistQuestion $message) : ChecklistQuestion
    {
        $questionRepo = $this->em->getRepository(ChecklistQuestion::class);
        /** @var ChecklistQuestion|null $question */
        $question = $questionRepo->find($message->getQuestionId());
        if (is_null($question)) {
            throw new NotFoundHttpException("Question with given ID has not been found");
        }
        if (($this->filter)($question)) {
            throw new BadRequestHttpException("Question with given ID is already done");
        }
        $lastPingDate = $question->getLastPingDate();
        if (!is_null($lastPingDate) && $lastPingDate->format('Y-m-d') === date('Y-m-d')) {
            throw new BadRequestHttpException("This question was already pinged today");
        }
        return $question;
    }

    private function getPinged(ChecklistQuestion $question) : Employee
    {
        $responsible = $question->getResponsible();
        if (is_null($question->getResponsible())) {
            throw new BadRequestHttpException("Question with given ID doesn't have any responsible person.");
        }
        return $responsible;
    }
}
