<?php

namespace Divante\Bundle\AdventureBundle\Infrastructure\Doctrine\Dbal;

use DateTimeInterface;
use Divante\Bundle\AdventureBundle\Entity\LeaveRequest;
use Divante\Bundle\AdventureBundle\Query\Report\ReportQuery;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Exception;

/**
 * Class DbalReportQuery
 * @package Divante\Bundle\AdventureBundle\Infrastructure\Doctrine\Dbal
 */
class DbalReportQuery implements ReportQuery
{

    /** @var array<string,string>  */
    private static array $fieldsMap = [
        'firstName' => 'e.name',
        'lastName' => 'e.last_name',
        'tribeName' => 'et.name',
        'positionName' => 'p.name',
        'levelName' => 'l.name',
        'projectName' => 'p2.name'
    ];

    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /** @inheritDoc */
    public function getByCriteria(array $criteria): array
    {
        $query = $criteria['query'];
        $start = $criteria['timestamp_gte'];
        $stop = $criteria['timestamp_lte'];
        $days = $this->createDaysArray($start, $stop);
        $resultArray = [];
        if ($criteria['view_mode'] === 'employee') {
            $employees = $this->getEmployeesByQuery($query);
            foreach ($employees as $employee) {
                $resultArray[] = [
                    'fullName' => $employee['name'] . ' ' . $employee['last_name'],
                    'id' => $employee['id'],
                    'days' => $this->fillDaysArrayInEmployees($days, (int)$employee['id']),
                    'items' => $this->fillProjectsArray(
                        $days,
                        $this->getProjectsByEmployeeId((int)$employee['id']),
                        (int)$employee['id']
                    )
                ];
            }
        } else {
            $projects = $this->getProjectsByQuery($query);
            foreach ($projects as $project) {
                $resultArray[] = [
                    'fullName' => $project['name'],
                    'id' => $project['id'],
                    'days' => $this->fillDaysArrayInProjects($days, (int)$project['id']),
                    'items' => $this->fillEmployeesArrays(
                        $days,
                        $this->getEmployeesByProjectId((int)$project['id']),
                        (int)$project['id']
                    )
                ];
            }
        }
        return $resultArray;
    }

    /**
     * @param DateTimeInterface[] $days
     * @param array<int,array<string,mixed>> $employees
     * @param int $projectId
     * @return array<int,array<string,mixed>>
     */
    protected function fillEmployeesArrays(array $days, array $employees, int $projectId)
    {
        $array = [];
        foreach ($employees as $employee) {
            $array[$employee['id']] = [
                'name' => $employee['name'] .' '. $employee['last_name'],
                'days' => $this->fillDaysArrayInProjects($days, $employee['id'], $projectId)
            ];
        }

        return $array;
    }

    /**
     * @param DateTimeInterface[] $days
     * @param array<int,array<string,string>> $projects
     * @param int $employeeId
     * @return array<int,array<string,mixed>>
     * @throws DBALException
     */
    protected function fillProjectsArray(array $days, array $projects, int $employeeId) : array
    {
        $array = [];

        foreach ($projects as $project) {
            $array[(int)$project['id']] = [
                'name' => $project['name'],
                'days' => $this->fillDaysArrayInEmployees($days, $employeeId, (int)$project['id'])
            ];
        }

        return $array;
    }

    /**
     * @param DateTimeInterface[] $days
     * @param int $employeeId
     * @param int|null $projectId
     * @return array<string,array<string,mixed>>
     * @throws DBALException
     */
    protected function fillDaysArrayInEmployees(array $days, int $employeeId, ?int $projectId = null) : array
    {
        $array = [];
        foreach ($days as $day) {
            if (is_null($projectId)) {
                $data = $this->getOccupancyInDay(
                    $day->getTimestamp(),
                    $employeeId
                );
            } else {
                $data = $this->getOccupancyInDayAndProject(
                    $day->getTimestamp(),
                    $employeeId,
                    $projectId
                );
            }
            $array[$day->format('Y-m-d')] = [
                'occupancy' => $data,
                'freeDay' => $this->getFreeDayInDay(
                    $day->getTimestamp(),
                    $employeeId
                )
            ];
        }

        return $array;
    }

    /**
     * @param DateTimeInterface[] $days
     * @param int $projectId
     * @param int|null $employeeId
     * @return array<string,array<string,mixed>>
     */
    protected function fillDaysArrayInProjects(array $days, int $projectId, ?int $employeeId = null) : array
    {
        $array = [];
        foreach ($days as $day) {
            if (is_null($employeeId)) {
                $data = $this->getOccupancyProjectInDay(
                    $day->getTimestamp(),
                    $projectId
                );
            } else {
                $data = $this->getOccupancyInDayAndProject(
                    $day->getTimestamp(),
                    $projectId,
                    $employeeId
                );
            }
            $array[$day->format('Y-m-d')] = [
                'occupancy' => $data,
                'freeDay' => false
            ];
            if (!is_null($employeeId)) {
                $array[$day->format('Y-m-d')]['freeDay'] = $this->getFreeDayInDay(
                    $day->getTimestamp(),
                    $projectId
                );
            }
        }

        return $array;
    }

    /**
     * @param int $employeeId
     * @return array<int,array<string,string>>
     */
    protected function getProjectsByEmployeeId(int $employeeId) : array
    {
        $sql = 'SELECT p.name, p.id FROM employee_project ep LEFT JOIN project p on ep.project_id = p.id
 WHERE ep.employee_id = :employee_id';
        return $this->connection->fetchAll($sql, ['employee_id' => $employeeId]);
    }

    /**
     * @param int $projectId
     * @return array<int,array<string,mixed>>
     */
    protected function getEmployeesByProjectId(int $projectId) : array
    {
        $sql = 'SELECT e.name, e.last_name, e.id FROM employee_project ep LEFT JOIN employee e on ep.employee_id = e.id
where ep.project_id =:project_id';
        return $this->connection->fetchAll($sql, ['project_id' => $projectId]);
    }

    /**
     * @param int $timestamp
     * @param int $employeeId
     * @return false|mixed
     * @throws DBALException
     */
    protected function getOccupancyInDay(int $timestamp, int $employeeId)
    {
        return $this->connection->fetchColumn(
            'SELECT SUM(eo.occupancy/60/60) FROM employee_occupancy eo WHERE  eo.employee_id = :id AND eo.date =:date',
            ['id' => $employeeId, 'date' => $timestamp]
        );
    }

    /**
     * @param int $timestamp
     * @param int $projectId
     * @return false|mixed
     * @throws DBALException
     */
    protected function getOccupancyProjectInDay(int $timestamp, int $projectId)
    {
        return $this->connection->fetchColumn(
            'SELECT SUM(eo.occupancy/60/60) FROM employee_occupancy eo WHERE  eo.project_id = :id AND eo.date =:date',
            ['id' => $projectId, 'date' => $timestamp]
        );
    }

    /**
     * @param int $timestamp
     * @param int $employeeId
     * @param int $projectId
     * @return false|mixed
     * @throws DBALException
     */
    protected function getOccupancyInDayAndProject(int $timestamp, int $employeeId, int $projectId)
    {
        return $this->connection->fetchColumn(
            'SELECT SUM(eo.occupancy/60/60) FROM employee_occupancy eo 
WHERE  eo.employee_id = :id AND eo.date =:date AND project_id =:pid',
            ['id' => $employeeId, 'date' => $timestamp, 'pid' => $projectId]
        );
    }

    /**
     * @param int $timestamp
     * @param int $employeeId
     * @return false|mixed
     * @throws DBALException
     */
    protected function getFreeDayInDay(int $timestamp, int $employeeId)
    {
        return $this->connection->fetchColumn(
            'SELECT lrd.hours FROM leave_request_day lrd  INNER JOIN leave_request lr 
                on lrd.request_id = lr.id WHERE lrd.date = :date and lrd.employee_id =:id
                AND (lr.status =:statusAccepted or lr.status =:statusPendingResignation)',
            [
                'id' => $employeeId,
                'date' => $timestamp,
                'statusAccepted' => LeaveRequest::REQUEST_STATUS_ACCEPTED,
                'statusPendingResignation' => LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION
            ]
        );
    }

    /**
     * @param int $start
     * @param int $stop
     * @return DateTimeInterface[]
     * @throws Exception
     */
    protected function createDaysArray(int $start, int $stop) : array
    {
        $timezone = new \DateTimeZone('Europe/Warsaw');
        $startDate = new \DateTime('@' . $start);
        $startDate->setTimezone($timezone);
        $stopDate = new \DateTime('@' . $stop);
        $stopDate = $stopDate->modify('+1 day');
        $stopDate->setTimezone($timezone);
        $period = new \DatePeriod(
            $startDate,
            new \DateInterval('P1D'),
            $stopDate
        );
        $dates = [];
        foreach ($period as $item) {
            $dates[] = $item;
        }
        return $dates;
    }
    /**
     * @param string|null $query
     * @return string
     */
    protected function mapFieldNameInQuery(?string $query): string
    {
        return str_replace(array_keys(self::$fieldsMap), array_values(self::$fieldsMap), $query);
    }

    /**
     * @param string|null $query
     * @return array<int,array<string,string>>
     */
    protected function getEmployeesByQuery(?string $query = null): array
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
         LEFT JOIN project p2 on ep.project_id = p2.id
         WHERE (e.hired_to is null OR e.hired_to > now())';
        $query = $this->mapFieldNameInQuery($query);
        if ($query !== null && $query !== '') {
            $sql = sprintf('%s AND %s', $sql, $query);
        }
        $sql = sprintf(' %s GROUP BY e.id', $sql);
        return $this->connection->fetchAll($sql);
    }

    /**
     * @param string|null $query
     * @return array<int,array<string,string>>
     */
    protected function getProjectsByQuery(?string $query = null): array
    {
        $sql = 'SELECT p2.id, p2.name FROM project p2';
        if (!is_null($query)) {
            $sql = $sql . ' WHERE '. $query;
        }
        $projects = $this->connection->fetchAll($this->mapFieldNameInQuery($sql));
        return $projects;
    }
}
