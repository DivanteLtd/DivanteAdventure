<?php


namespace Divante\Bundle\AdventureBundle\Services\Decorators;

use Divante\Bundle\AdventureBundle\Entity\Level as LevelEntity;

class Level extends AbstractDecorator
{

    public function decorate(?int $id): string
    {
        if (is_null($id)) {
            return '';
        }
        $employeeRepo = $this->getEntityManager()->getRepository(LevelEntity::class);
        $level = $employeeRepo->find($id);
        return $level->getName();
    }
}
