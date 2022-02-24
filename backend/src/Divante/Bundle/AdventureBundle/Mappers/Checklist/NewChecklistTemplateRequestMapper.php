<?php

namespace Divante\Bundle\AdventureBundle\Mappers\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistInterface;
use Divante\Bundle\AdventureBundle\Mappers\Mapper;
use Divante\Bundle\AdventureBundle\Message\Checklist\CreateChecklistTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class NewChecklistTemplateRequestMapper implements Mapper
{

    /**
     * @param Request $request
     * @return array<string,mixed>
     */
    public function mapEntity($request): array
    {
        return [
            'type' => $this->getType($request),
            'namePl' => $this->getNamePl($request),
            'nameEn' => $this->getNameEn($request),
        ];
    }

    public function mapToMessage(Request $request) : CreateChecklistTemplate
    {
        $data = $this->mapEntity($request);
        return new CreateChecklistTemplate(
            $data['type'],
            $data['namePl'],
            $data['nameEn'],
        );
    }

    private function getType(Request $request) : int
    {
        $type = $request->get('type');
        if (is_null($type)) {
            throw new BadRequestHttpException("Field 'type' is required");
        }
        if (!is_int($type)) {
            throw new BadRequestHttpException("Field 'type' must be a number");
        }
        /** @var int $type */
        $correctValues = [
            ChecklistInterface::TYPE_UNITED,
            ChecklistInterface::TYPE_DISTRIBUTED,
        ];
        if (!in_array($type, $correctValues)) {
            $correctValuesStr = implode(', ', $correctValues);
            throw new BadRequestHttpException("Correct values for field 'type' are: $correctValuesStr");
        }
        return $type;
    }

    private function getNamePl(Request $request) : string
    {
        $name = $request->get('namePl');
        if (is_null($name)) {
            throw new BadRequestHttpException("Field 'namePl' is required");
        }
        return (string)$name;
    }

    private function getNameEn(Request $request) : string
    {
        $name = $request->get('nameEn');
        if (is_null($name)) {
            throw new BadRequestHttpException("Field 'nameEn' is required");
        }
        return (string)$name;
    }
}
