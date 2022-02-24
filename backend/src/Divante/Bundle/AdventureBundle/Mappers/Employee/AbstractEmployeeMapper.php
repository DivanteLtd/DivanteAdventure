<?php

namespace Divante\Bundle\AdventureBundle\Mappers\Employee;

use Divante\Bundle\AdventureBundle\Entity\Employee;

abstract class AbstractEmployeeMapper
{
    /**
     * @param Employee $entity
     * @return array<string,mixed>
     */
    abstract public function mapEmployeeToJson(Employee $entity) : array;

    /**
     * @param Employee $employee
     * @return array<string,mixed>
     */
    final public function __invoke(Employee $employee) : array
    {
        return $this->mapEmployeeToJson($employee);
    }
}
