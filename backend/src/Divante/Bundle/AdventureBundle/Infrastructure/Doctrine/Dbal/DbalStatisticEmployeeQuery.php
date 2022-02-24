<?php

namespace Divante\Bundle\AdventureBundle\Infrastructure\Doctrine\Dbal;

use Divante\Bundle\AdventureBundle\Query\Statistic\EmployeeGeneralView;
use Divante\Bundle\AdventureBundle\Query\Statistic\EmployeeQuery;
use Divante\Bundle\AdventureBundle\Query\Statistic\EmployeeView;
use Doctrine\DBAL\Connection;

class DbalStatisticEmployeeQuery implements EmployeeQuery
{
    /** @var Connection */
    private $connection;

    /**
     * DbalStatisticEmployeeQuery constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getAll(): array
    {
        $result = $this->connection->fetchAll('SELECT tribe_name, concat(year,\'-\',month) as date,
 number_of_enter, number_of_leave, number_of_work from tribe_rotation_history t;');
        return array_map(function ($val) {
            return (new EmployeeView(
                $val['tribe_name'],
                (new \DateTime($val['date'])),
                $val['number_of_enter'],
                $val['number_of_leave'],
                $val['number_of_work']
            ));
        }, $result);
    }

    public function getAllGeneral(): array
    {
        $result = $this->connection->fetchAll('SELECT t.name, t.last_name,
 DATE_FORMAT(t.hired_at, "%Y-%m") as hired_at,
       DATE_FORMAT(t.hired_to, "%Y-%m") as hired_to, t.gender, t.tribe_id, t2.name as tribe_name,
        eec.who_ended_cooperation, eec.dismiss_date
FROM employee t INNER JOIN tribe t2 on t.tribe_id = t2.id 
LEFT JOIN employee_end_cooperation eec on eec.employee_id = t.id WHERE hired_at is not null and t.tribe_id is not null
 and gender is not null;');
        return array_map(function ($val) {
            return (new EmployeeGeneralView(
                $val['name'],
                $val['last_name'],
                $val['hired_at'],
                $val['hired_to'],
                $val['gender'],
                $val['tribe_id'],
                $val['tribe_name'],
                $val['who_ended_cooperation'],
                $val['dismiss_date']
            ));
        }, $result);
    }
}
