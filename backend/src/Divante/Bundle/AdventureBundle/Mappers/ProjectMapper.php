<?php
/**
 * Created by PhpStorm.
 * User: norbert
 * Date: 21.12.18
 * Time: 08:55
 */

namespace Divante\Bundle\AdventureBundle\Mappers;

use Divante\Bundle\AdventureBundle\Entity\Project;

class ProjectMapper implements Mapper
{

    /**
     * @param Project $entity
     * @return array<string,mixed>
     */
    public function mapEntity($entity): array
    {
        return [
            "id" => $entity->getId(),
            "name" => $entity->getName()
        ];
    }
}
