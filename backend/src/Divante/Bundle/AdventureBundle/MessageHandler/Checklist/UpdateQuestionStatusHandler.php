<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Checklist;

use DateTime;
use Divante\Bundle\AdventureBundle\Entity\Checklist\Checklist;
use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistQuestion;
use Divante\Bundle\AdventureBundle\Message\Checklist\UpdateQuestionStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpdateQuestionStatusHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(UpdateQuestionStatus $message) : void
    {
        $question = $this->getQuestion($message);
        $this->verifyRoles($message, $question);
        $checklist = $question->getChecklist();

        if (!array_key_exists($message->getStatus(), $question->getPossibleStatuses())) {
            throw new BadRequestHttpException("Status with given ID has not been found");
        }
        $selectedStatus = $question->getPossibleStatuses()[$message->getStatus()];
        $isDone = (boolean)($selectedStatus['done'] ?? false);
        $question
            ->setCurrentStatus($message->getStatus())
            ->setUpdatedAt()
            ->setCheckedAt($isDone ? new DateTime() : null);

        $checklistDone = true;
        /** @var ChecklistQuestion $questionEntry */
        foreach ($checklist->getQuestions()->toArray() as $questionEntry) {
            if ($questionEntry->getId() === $question->getId()) {
                $questionEntry = $question;
            }
            $statuses = $questionEntry->getPossibleStatuses();
            $currentStatus = $statuses[$questionEntry->getCurrentStatus()];
            if (!($currentStatus['done'] ?? false)) {
                $checklistDone = false;
            }
        }
        if ($checklistDone) {
            $checklist->setFinishedAt(new DateTime());
        }
    }

    private function verifyRoles(UpdateQuestionStatus $message, ChecklistQuestion $question): void
    {
        $checklist = $question->getChecklist();
        $isUnited = $checklist->getType() === Checklist::TYPE_UNITED;
        $isDistributed = $checklist->getType() === Checklist::TYPE_DISTRIBUTED;
        $employee = $message->getEmployee();
        $questionOwner = $question->getResponsible();
        $user = $employee->getUser();
        $checklistOwners = $checklist->getOwners()->toArray();
        if (!is_null($user) && $user->hasRole('ROLE_HR')) {
            return;
        }
        if ($isUnited && (empty($checklistOwners) || !in_array($employee, $checklistOwners))) {
            throw new AccessDeniedHttpException(
                "Question is in checklist with type 'UNITED', but user is not an owner of that checklist"
            );
        }
        if ($isDistributed && (is_null($questionOwner) || $questionOwner->getId() !== $employee->getId())) {
            throw new AccessDeniedHttpException(
                "Question is in checklist with type 'DISTRIBUTED', but user is not responsible for that question"
            );
        }
    }

    private function getQuestion(UpdateQuestionStatus $message) : ChecklistQuestion
    {
        $repo = $this->em->getRepository(ChecklistQuestion::class);
        /** @var ChecklistQuestion|null $question */
        $question = $repo->find($message->getQuestionId());
        if (is_null($question)) {
            throw new NotFoundHttpException("Checklist question with given ID has not been found");
        }
        return $question;
    }
}
