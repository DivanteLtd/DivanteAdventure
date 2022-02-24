<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistTemplate;
use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistTemplateQuestion;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Message\Checklist\EditChecklistTemplateQuestion;
use Divante\Bundle\AdventureBundle\Services\PossibleStatusesVerificator;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EditChecklistTemplateQuestionHandler
{
    private EntityManagerInterface $em;
    private PossibleStatusesVerificator $validator;

    public function __construct(EntityManagerInterface $em, PossibleStatusesVerificator $validator)
    {
        $this->em = $em;
        $this->validator = $validator;
    }

    public function __invoke(EditChecklistTemplateQuestion $message) : void
    {
        $question = $this->getQuestion($message);
        $responsible = $this->getResponsible($message);
        if ($question->getChecklist()->getType() === ChecklistTemplate::TYPE_UNITED && !is_null($responsible)) {
            throw new BadRequestHttpException(
                "Responsible employee ID has been passed, but template uses type 'united'"
            );
        }
        $statuses = $this->getStatuses($message);
        $question->setNamePl($message->getNamePl() ?? $question->getNamePl());
        $question->setNameEn($message->getNameEn() ?? $question->getNameEn());
        $question->setDescriptionPl($message->getDescriptionPl() ?? $question->getDescriptionPl());
        $question->setDescriptionEn($message->getDescriptionEn() ?? $question->getDescriptionEn());
        $question->setPossibleStatuses($statuses ?? $question->getPossibleStatuses());
        $question->setResponsible($responsible ?? $question->getResponsible());
        $question->setUpdatedAt();
    }

    /**
     * @param EditChecklistTemplateQuestion $message
     * @return array<int,array<string,string>>|null
     */
    private function getStatuses(EditChecklistTemplateQuestion $message) : ?array
    {
        $statuses = $message->getPossibleStatuses();
        if (is_null($statuses)) {
            return null;
        }
        try {
            $this->validator->verify($statuses);
            return $statuses;
        } catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e, $e->getCode());
        }
    }

    private function getQuestion(EditChecklistTemplateQuestion $message) : ChecklistTemplateQuestion
    {
        $repo = $this->em->getRepository(ChecklistTemplateQuestion::class);
        /** @var ChecklistTemplateQuestion|null $question */
        $question = $repo->find($message->getQuestionId());
        if (is_null($question)) {
            throw new NotFoundHttpException("Checklist question with given ID has not been found");
        }
        return $question;
    }

    private function getResponsible(EditChecklistTemplateQuestion $message) : ?Employee
    {
        $responsibleId = $message->getResponsibleId();
        if (is_null($responsibleId)) {
            return null;
        }
        $repo = $this->em->getRepository(Employee::class);
        /** @var Employee|null $employee */
        $employee = $repo->find($responsibleId);
        if (is_null($employee)) {
            throw new NotFoundHttpException("Employee with given ID has not been found");
        }
        return $employee;
    }
}
