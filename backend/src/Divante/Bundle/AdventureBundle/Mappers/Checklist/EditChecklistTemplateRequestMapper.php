<?php

namespace Divante\Bundle\AdventureBundle\Mappers\Checklist;

use Divante\Bundle\AdventureBundle\Mappers\Mapper;
use Divante\Bundle\AdventureBundle\Message\Checklist\EditChecklistTemplate;
use Symfony\Component\HttpFoundation\Request;

class EditChecklistTemplateRequestMapper implements Mapper
{

    /**
     * @param Request $entity
     * @return array<string,mixed>
     */
    public function mapEntity($entity): array
    {
        return [
            'namePl' => $this->getNamePl($entity),
            'nameEn' => $this->getNameEn($entity),
        ];
    }

    public function mapToMessage(Request $request, int $id) : EditChecklistTemplate
    {
        $data = $this->mapEntity($request);
        return new EditChecklistTemplate(
            $id,
            $data['namePl'],
            $data['nameEn'],
        );
    }

    private function getNamePl(Request $request) : ?string
    {
        return $request->get('namePl', null);
    }

    private function getNameEn(Request $request) : ?string
    {
        return $request->get('nameEn', null);
    }
}
