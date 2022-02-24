<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\Mappers\FAQ;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\FAQ\FAQCategory;
use Divante\Bundle\AdventureBundle\Mappers\AbstractDoctrineAwareMapper;
use Divante\Bundle\AdventureBundle\Mappers\Mapper;

class FAQCategoryDetailsMapper extends AbstractDoctrineAwareMapper implements Mapper
{
    /**
     * @param FAQCategory[] $categories
     * @return array<int,array<string,mixed>>
     * @throws \Exception
     */
    public function mapEntity($categories): array
    {
        $json = [];
        /** @var FAQCategory $category */
        foreach ($categories as $category) {
            $responsibleEmployees = $category->getEmployee();
            /** @var Employee $responsibleEmployee */
            $tmp = [];
            foreach ($responsibleEmployees as $responsibleEmployee) {
                $tmp[] = [
                    'id' => $responsibleEmployee->getId(),
                    'name' => $responsibleEmployee->getName(),
                    'lastName' => $responsibleEmployee->getLastName(),
                    'photo' => $responsibleEmployee->getPhoto(),
                ];
            }
            $json[] = [
                'id' => $category->getId(),
                'namePl' => $category->getNamePl(),
                'nameEn' => $category->getNameEn(),
                'responsibles' => $tmp,
            ];
        }
        return $json;
    }
}
