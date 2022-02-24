<?php


namespace Divante\Bundle\AdventureBundle\Services\Decorators;

use Divante\Bundle\AdventureBundle\Entity\Employee;

class Contract extends AbstractDecorator
{

    public function decorate(?int $id): string
    {
        return (string)Employee::getContractNameById($id);
    }
}
