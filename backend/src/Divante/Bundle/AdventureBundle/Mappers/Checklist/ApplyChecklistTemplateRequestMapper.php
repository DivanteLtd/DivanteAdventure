<?php

namespace Divante\Bundle\AdventureBundle\Mappers\Checklist;

use Divante\Bundle\AdventureBundle\Message\Checklist\ApplyChecklistTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ApplyChecklistTemplateRequestMapper
{
    public function mapToMessage(Request $request, int $templateId, int $employeeId = -1) : ApplyChecklistTemplate
    {
        return new ApplyChecklistTemplate(
            $templateId,
            $this->getOwnerId($request),
            $this->getSubjectId($request),
            $request->get('hidden', false),
            $employeeId,
            $this->getDueDate($request)
        );
    }

    private function getOwnerId(Request $request) : array
    {
        $ids = $request->get('ownerId', []);
        if (empty($ids)) {
            return [];
        }
        if (!is_array($ids)) {
            throw new BadRequestHttpException("Field 'ownerId' must have type 'array' ");
        }
        return $ids;
    }

    private function getSubjectId(Request $request) : int
    {
        $id = $request->get('subjectId', null);
        if (is_null($id)) {
            throw new BadRequestHttpException("Field 'subjectId' is required");
        }
        if (!is_int($id)) {
            throw new BadRequestHttpException("Field 'subjectId' must have type 'int'");
        }
        return $id;
    }

    private function getDueDate(Request $request) : string
    {
        $dueDate = $request->get('dueDate', null);
        if (is_null($dueDate)) {
            throw new BadRequestHttpException("Field 'dueDate' is required");
        }
        return $dueDate;
    }
}
