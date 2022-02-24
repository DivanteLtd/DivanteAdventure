<?php
/**
 * Created by PhpStorm.
 * User: pawel
 * Date: 11.01.19
 * Time: 14:10
 */
namespace Divante\Bundle\AdventureBundle\Query\Employee;

use Divante\Bundle\AdventureBundle\Entity\Employee;

interface EmployeeQuery
{
    /**
     * @param string|null $query
     * @return EmployeeView[]
     */
    public function getAllForSchedulerByQuery(?string $query = '') : array;
    public function getById(int $id) : Employee;
}
