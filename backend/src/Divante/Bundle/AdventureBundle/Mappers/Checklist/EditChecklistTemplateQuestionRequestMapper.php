<?php

namespace Divante\Bundle\AdventureBundle\Mappers\Checklist;

use Divante\Bundle\AdventureBundle\Message\Checklist\EditChecklistTemplateQuestion;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class EditChecklistTemplateQuestionRequestMapper
{
    public function mapToMessage(Request $request, int $templateId, int $questionId) : EditChecklistTemplateQuestion
    {
        return new EditChecklistTemplateQuestion(
            $templateId,
            $questionId,
            $this->getNamePl($request),
            $this->getNameEn($request),
            $this->getDescriptionPl($request),
            $this->getDescriptionEn($request),
            $this->getPossibleStatuses($request),
            $this->getResponsibleId($request),
        );
    }

    private function getNamePl(Request $request) : ?string
    {
        /** @var string|null $val */
        $val = $request->get('namePl', null);
        return $val;
    }

    private function getNameEn(Request $request) : ?string
    {
        /** @var string|null $val */
        $val = $request->get('nameEn', null);
        return $val;
    }

    private function getDescriptionPl(Request $request) : ?string
    {
        /** @var string|null $val */
        $val = $request->get('descriptionPl', null);
        return $val;
    }

    private function getDescriptionEn(Request $request) : ?string
    {
        /** @var string|null $val */
        $val = $request->get('descriptionEn', null);
        return $val;
    }

    /**
     * @param Request $request
     * @return array<int,array<string,string>>|null
     */
    private function getPossibleStatuses(Request $request) : ?array
    {
        /** @var array<int,array<string,string>>|null $statuses */
        $statuses = $request->get('possibleStatuses', null);
        if (is_null($statuses)) {
            return null;
        }
        if (!is_array($statuses)) {
            throw new BadRequestHttpException("Field 'possibleStatuses' must have type 'array' or null");
        }
        return $statuses;
    }

    private function getResponsibleId(Request $request) : ?int
    {
        /** @var int|null $id */
        $id = $request->get('responsibleId', null);
        if (is_null($id)) {
            return null;
        }
        if (!is_int($id)) {
            throw new BadRequestHttpException("Field 'responsibleId' must have type 'integer' or null");
        }
        return $id;
    }
}
