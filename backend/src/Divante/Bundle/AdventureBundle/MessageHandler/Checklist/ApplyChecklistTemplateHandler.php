<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Checklist;

use DateTime;
use Divante\Bundle\AdventureBundle\Entity\Checklist\Checklist;
use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistQuestion;
use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistTemplate;
use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistTemplateQuestion;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Events\Checklist\ChecklistAssignedEvent;
use Divante\Bundle\AdventureBundle\Message\Checklist\ApplyChecklistTemplate;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApplyChecklistTemplateHandler
{
    private EntityManagerInterface $em;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $eventDispatcher)
    {
        $this->em = $em;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(ApplyChecklistTemplate $message) : void
    {
        $template = $this->getTemplate($message);
        $subject = $this->getSubject($message);
        $owners = $this->getOwners($message);
        $hidden = $message->isHidden();
        $dueDate = $message->getDueDate();

        $checklist = (new Checklist())
            ->setType($template->getType())
            ->setNamePl($this->applyTemplate($template->getNamePl(), $owners, $subject))
            ->setNameEn($this->applyTemplate($template->getNameEn(), $owners, $subject))
            ->setSubject($subject)
            ->setOwners($owners)
            ->setHidden($hidden)
            ->setStartedAt(new DateTime())
            ->setFinishedAt(null)
            ->setDueDate((new DateTime($dueDate))->setTime(23, 59, 59))
            ->setCreatedAt()
            ->setUpdatedAt();

        /** @var ChecklistTemplateQuestion $templateQuestion */
        foreach ($template->getQuestions()->toArray() as $templateQuestion) {
            $defaultStatus = -1;
            foreach ($templateQuestion->getPossibleStatuses() as $status => $data) {
                if ($data['default'] ?? false) {
                    $defaultStatus = $status;
                }
            }
            $question = new ChecklistQuestion();
            $question->setNamePl($templateQuestion->getNamePl());
            $question->setNameEn($templateQuestion->getNameEn());
            $question->setDescriptionPl($templateQuestion->getDescriptionPl());
            $question->setDescriptionEn($templateQuestion->getDescriptionEn());
            $question->setPossibleStatuses($templateQuestion->getPossibleStatuses());
            $question->setResponsible($templateQuestion->getResponsible());
            $question->setCurrentStatus($defaultStatus);
            $question->setCheckedAt(null);
            $question->setCreatedAt();
            $question->setUpdatedAt();
            $this->em->persist($question);
            $checklist->addQuestion($question);
        }
        $this->em->persist($checklist);

        /** @var Employee $userEmployee */
        $userEmployee = $this->em->getRepository(Employee::class)->find($message->getUserEmployeeId());
        $this->eventDispatcher->dispatch(new ChecklistAssignedEvent($checklist, $userEmployee));
    }

    private function applyTemplate(string $template, array $owners, Employee $subject) : string
    {
        $ownerNames = array_map(fn(Employee $owner): string => sprintf(
            '%s %s',
            $owner->getName(),
            $owner->getLastName()
        ), $owners);
        $subjectName = sprintf("%s %s", $subject->getName(), $subject->getLastName());
        return str_replace(
            [ '%OWNER%', '%SUBJECT%' ],
            [ implode(',', $ownerNames), $subjectName ],
            $template,
        );
    }

    private function getSubject(ApplyChecklistTemplate $message) : Employee
    {
        $repo = $this->em->getRepository(Employee::class);
        /** @var Employee|null $employee */
        $employee = $repo->find($message->getSubjectId());
        if (is_null($employee)) {
            throw new NotFoundHttpException("Subject employee with given ID has not been found");
        }
        return $employee;
    }

    private function getOwners(ApplyChecklistTemplate $message) : array
    {
        if (empty($message->getOwnerIds())) {
            return [];
        }
        /** @var array $employees */
        $employees = $this->getEmployees($message->getOwnerIds());

        return $employees;
    }

    private function getEmployees(array $ids) : array
    {
        $repo = $this->em->getRepository(Employee::class);
        /** @var array $employees */
        $employees = $repo->findBy(['id' => $ids]);
        if (empty($employees)) {
            throw new NotFoundHttpException("Owner employee with given ID has not been found");
        }
        return $employees;
    }

    private function getTemplate(ApplyChecklistTemplate $message) : ChecklistTemplate
    {
        $repo = $this->em->getRepository(ChecklistTemplate::class);
        $id = $message->getTemplateId();
        /** @var ChecklistTemplate|null $template */
        $template = $repo->find($id);
        if (is_null($template)) {
            throw new NotFoundHttpException("Checklist template with given ID has not been found");
        }
        return $template;
    }
}
