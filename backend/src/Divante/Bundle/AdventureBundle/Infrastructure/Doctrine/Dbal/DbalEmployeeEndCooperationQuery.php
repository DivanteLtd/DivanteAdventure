<?php

namespace Divante\Bundle\AdventureBundle\Infrastructure\Doctrine\Dbal;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Query\Employee\EmployeeEndCooperationQuery;
use Divante\Bundle\AdventureBundle\Query\Employee\EmployeeEndCooperationView;
use Doctrine\DBAL\Connection;

class DbalEmployeeEndCooperationQuery implements EmployeeEndCooperationQuery
{
    protected Connection $conn;

    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    /** @inheritDoc */
    public function getAll(): array
    {
        $employees = $this->conn->fetchAll(
            'select e.id as employee_id, e.name, e.last_name, e.contract_id as contract_id, e.email, c.* 
from employee_end_cooperation c
    INNER JOIN employee e on c.employee_id = e.id'
        );
        return array_map(function ($val) {
            return new EmployeeEndCooperationView(
                $val["id"],
                $val['employee_id'],
                $val['name'],
                $val['last_name'],
                Employee::getContractNameById($val['contract_id']),
                $val['next_company'],
                $val['who_ended_cooperation'],
                $val['exit_interview'],
                $val['checklist'],
                $val['comment'],
                $val['dismiss_date'],
                $val['email']
            );
        }, $employees);
    }
}
