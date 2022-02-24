<?php
/**
 * Created by PhpStorm.
 * User: pawel
 * Date: 11.01.19
 * Time: 14:26
 */
namespace Divante\Bundle\AdventureBundle\Infrastructure\Doctrine\Dbal;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Query\Employee\EmployeeQuery;
use Divante\Bundle\AdventureBundle\Query\Employee\EmployeeView;
use Divante\Bundle\AdventureBundle\Services\FilterParser\Field;
use Divante\Bundle\AdventureBundle\Services\FilterParser\Parser;
use Doctrine\DBAL\Connection;

class DbalEmployeeQuery implements EmployeeQuery
{
    /** @var Field[]|null */
    private static ?array $fields = null;
    private Connection $conn;
    private Parser $parser;

    public function __construct(Connection $conn, Parser $parser)
    {
        $this->conn = $conn;
        $this->parser = $parser;
    }

    /** @return Field[] */
    protected function getFields() : array
    {
        if (is_null(self::$fields)) {
            self::$fields = [
                new Field("firstName", 'e', 'name'),
                new Field("lastName", 'e', 'last_name'),
                new Field("tribeName", 'et', 'name'),
                new Field("positionName", 'p', 'name'),
                new Field("levelName", 'l', 'name'),
                new Field("projectName", 'p2', 'name'),
            ];
        }
        return self::$fields;
    }

    /** @inheritdoc */
    public function getAllForSchedulerByQuery(?string $query = ''): array
    {
        $sql = 'SELECT e.id,
       e.name,
       e.last_name,
       e.job_time_value,
       e.hired_to,
       CONCAT(\'tribe/\',et.id) as tribe,
       (
           SELECT GROUP_CONCAT(esa.skill_area_id)
           FROM employee_skill_area esa
           WHERE esa.employee_id = e.id
       ) AS skills,
       (
           SELECT CONCAT(\'position/\',p.id)
           FROM position p
           WHERE p.id = e.position_id
       ) AS positions,
       et.name as tribeName,
       p.name AS positionName,
       l.name AS levelName,
       p2.name as projectName
FROM employee e
         LEFT JOIN tribe et on e.tribe_id = et.id
         LEFT JOIN level l on e.level_id = l.id
         LEFT JOIN position p on e.position_id = p.id
         LEFT JOIN employee_project ep on e.id = ep.employee_id
         LEFT JOIN project p2 on ep.project_id = p2.id';
        if (!empty($query)) {
            $query = $this->parser->parse($query, $this->getFields());
            if (!empty($query)) {
                $sql = $sql . ' WHERE ' . $query;
            }
        }
        $sql = $sql . ' GROUP BY e.id';
        $employees = $this->conn->fetchAll($sql);
        return array_map(function ($employeeData) {
            $tmp = [$employeeData['tribe']];
            $skills = array_map(function ($skill) {
                return "skill/$skill";
            }, explode(',', $employeeData['skills']));
            $filters = array_merge($skills, $tmp);
            return new EmployeeView(
                $employeeData['id'],
                $employeeData['name'],
                $employeeData['last_name'],
                $employeeData['job_time_value'],
                $employeeData['hired_to'],
                $employeeData['tribeName'],
                $employeeData['positionName'],
                $employeeData['levelName'],
                $employeeData['projectName'],
                array_merge($filters, [$employeeData['positions']])
            );
        }, $employees);
    }

    public function getById(int $id): Employee
    {
        return new Employee();
    }

    public function getAllForListByFields(array $fields) :?array
    {
        $qb = $this->conn->createQueryBuilder();
        foreach ($fields as $key => $field) {
            $qb->addSelect($key . ' as ' . '"' . $field . '"');
        }
        $qb->from('employee');
        return $qb->execute()->fetchAll();
    }
}
