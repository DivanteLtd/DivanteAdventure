<?php


namespace Divante\Bundle\AdventureBundle\Services\Decorators;

use Divante\Bundle\AdventureBundle\Entity\Position as PositionEntity;

class Position extends AbstractDecorator
{

    public function decorate(?int $id): string
    {
        if (is_null($id)) {
            return '';
        }
        $employeeRepo = $this->getEntityManager()->getRepository(PositionEntity::class);
        $employee = $employeeRepo->find($id);
        return $employee->getName();
    }
}
