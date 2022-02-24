<?php
/**
 * Created by PhpStorm.
 * User: pawel
 * Date: 16.01.19
 * Time: 13:22
 */

namespace Divante\Bundle\AdventureBundle\Infrastructure\Doctrine\Dbal;

use Divante\Bundle\AdventureBundle\Query\EmployeeProject\EmployeeProjectQuery;
use Divante\Bundle\AdventureBundle\Query\EmployeeProject\EmployeeProjectView;
use Doctrine\DBAL\Connection;

class DbalEmployeeProjectQuery implements EmployeeProjectQuery
{
    /** @var Connection */
    protected $conn;

    private const GET_ALL_SQL = <<<SQL
        SELECT ep.id,
               ep.employee_id,
               ep.project_id,
               e.name AS `employee_name`,
               e.last_name AS `employee_last_name`,
               p.name AS `project_name`,
               p.started_at,
               p.ended_at,
               ep.date_from, 
               ep.date_to,
               ep.overtime,
               p.archived
        FROM employee_project AS ep
        JOIN employee AS e
            ON e.id = ep.employee_id
        JOIN project AS p
            ON p.id = ep.project_id
        WHERE ep.date_from != 'a:0:{}'
        GROUP BY ep.id
SQL;


    /**
     * DbalEmployeeProjectQuery constructor.
     * @param Connection $conn
     */
    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    /** @inheritdoc */
    public function getAll() :array
    {
        $pairs = $this->conn->fetchAll(self::GET_ALL_SQL);
        return array_map(function ($pair) {
            $dateFrom = unserialize($pair['date_from']);
            $dateTo = unserialize($pair['date_to']);
            if (!is_array($dateFrom)) {
                $dateFrom = [];
            }
            if (!is_array($dateTo)) {
                $dateTo = [];
            }
            return new EmployeeProjectView(
                $pair['id'],
                $pair['employee_id'],
                $pair['project_id'],
                $pair['employee_name'],
                $pair['employee_last_name'],
                $pair['project_name'],
                $pair['started_at'],
                $pair['ended_at'],
                $dateFrom,
                $dateTo,
                boolval($pair['overtime']),
                $pair['archived']
            );
        }, $pairs);
    }

    /** @inheritdoc */
    public function getById(int $id): EmployeeProjectView
    {
        $pair = $this->conn->fetchAll(
            'SELECT ep.id, ep.employee_id, ep.project_id, e.name AS `employee_name`, 
                  e.last_name AS `employee_last_name`, p.name AS `project_name`, p.started_at, p.ended_at, p.archived,
                  ep.date_from, ep.date_to, ep.overtime FROM employee_project ep JOIN employee e
                 ON e.id = ep.employee_id JOIN project p ON p.id = ep.project_id WHERE ep.id = :id GROUP BY ep.id',
            ['id' => $id]
        );
        return new EmployeeProjectView(
            $pair[0]['id'],
            $pair[0]['employee_id'],
            $pair[0]['project_id'],
            $pair[0]['employee_name'],
            $pair[0]['employee_last_name'],
            $pair[0]['project_name'],
            $pair[0]['started_at'],
            $pair[0]['ended_at'],
            unserialize($pair[0]['date_from']),
            unserialize($pair[0]['date_to']),
            $pair[0]['overtime'],
            $pair[0]['archived']
        );
    }
}
