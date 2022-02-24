<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistTemplate;
use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistTemplateQuestion;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Message\Checklist\CreateChecklistTemplateQuestion;
use Divante\Bundle\AdventureBundle\Services\PossibleStatusesVerificator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CreateChecklistTemplateQuestionHandler
{
    private EntityManagerInterface $em;
    private PossibleStatusesVerificator $statusesVerificator;

    public function __construct(EntityManagerInterface $em, PossibleStatusesVerificator $statusesVerificator)
    {
        $this->em = $em;
        $this->statusesVerificator = $statusesVerificator;
    }

    public function __invoke(CreateChecklistTemplateQuestion $message) : void
    {
        $possibleStatuses = $message->getPossibleStatuses();
        try {
            $this->statusesVerificator->verify($possibleStatuses);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e, $e->getCode());
        }
        $template = $this->getTemplate($message);
        $employee = $this->getResponsibleEmployee($message);
        if ($template->getType() === ChecklistTemplate::TYPE_UNITED && !is_null($employee)) {
            throw new BadRequestHttpException(
                "Responsible employee ID has been passed, but template uses type 'united'"
            );
        }
        if ($template->getType() === ChecklistTemplate::TYPE_DISTRIBUTED && is_null($employee)) {
            throw new BadRequestHttpException(
                "Responsible employee ID has not been passed, but template uses type 'distributed'"
            );
        }

        $question = new ChecklistTemplateQuestion();
        $question->setResponsible($employee);
        $question->setChecklist($template);
        $question->setPossibleStatuses($message->getPossibleStatuses());
        $question->setNamePl($message->getNamePl());
        $question->setNameEn($message->getNameEn());
        $question->setDescriptionPl($message->getDescriptionPl());
        $question->setDescriptionEn($message->getDescriptionEn());
        $question->setCreatedAt();
        $question->setUpdatedAt();
        $this->em->persist($question);
    }

    private function getTemplate(CreateChecklistTemplateQuestion $message) : ChecklistTemplate
    {
        $repo = $this->em->getRepository(ChecklistTemplate::class);
        /** @var ChecklistTemplate|null $template */
        $template = $repo->find($message->getTemplateId());
        if (is_null($template)) {
            throw new NotFoundHttpException("Template with given ID has not been found");
        }
        return $template;
    }

    private function getResponsibleEmployee(CreateChecklistTemplateQuestion $message) : ?Employee
    {
        $employeeId = $message->getResponsibleId();
        if (is_null($employeeId)) {
            return null;
        }
        $repo = $this->em->getRepository(Employee::class);
        /** @var Employee|null $employee */
        $employee = $repo->find($message->getResponsibleId());
        if (is_null($employee)) {
            throw new NotFoundHttpException("Employee with given ID has not been found");
        }
        return $employee;
    }
}
