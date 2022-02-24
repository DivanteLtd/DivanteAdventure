<?php

namespace Divante\Bundle\AdventureBundle\Infrastructure\Doctrine\Dbal;

use DateInterval;
use DatePeriod;
use DateTime;
use Divante\Bundle\AdventureBundle\Query\Statistic\CompanyPerEmployeeView;
use Divante\Bundle\AdventureBundle\Query\Statistic\CompanyQuery;
use Divante\Bundle\AdventureBundle\Query\Statistic\CompanyView;
use Divante\Bundle\AdventureBundle\Supplier\FreeDaysSupplier;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

class DbalStatisticCompanyQuery implements CompanyQuery
{

    private EntityManagerInterface $em;
    private Connection $connection;
    private FreeDaysSupplier $supplier;
    private const MONTHS = [
        '01',
        '02',
        '03',
        '04',
        '05',
        '06',
        '07',
        '08',
        '09',
        '10',
        '11',
        '12'
    ];
    public function __construct(EntityManagerInterface $em, Connection $connection, FreeDaysSupplier $supplier)
    {
        $this->em = $em;
        $this->connection = $connection;
        $this->supplier = $supplier;
    }

    public function getStatsByYearAndTribes(int $year, array $tribes) :array
    {
        $months = [];
        $nowDate = new DateTime();
        foreach (self::MONTHS as $month) {
            if ($year.$month <= $nowDate->format('Ym')) {
                $date = new DateTime(sprintf('%s-%s-01', $year, $month));
                $firstDay = clone $date;
                $lastDay = $date->modify('last day of this month 23:59:59');
                $employees = $this->getEmployees($firstDay, $lastDay, $tribes);
                $possibleSecondsInMoth = $this->possibleSecondsInMonth($firstDay, $lastDay, $employees);
                $billableSecondsInMonth = $this->billableOrNoBillableSecondsInMonth(
                    $firstDay,
                    $lastDay,
                    $employees,
                    true
                );
                $noBillableSecondsInMonth = $this->billableOrNoBillableSecondsInMonth(
                    $firstDay,
                    $lastDay,
                    $employees,
                    false
                );
                $plannedSecondsInMonth = $this->plannedSecondsInMonth($firstDay, $lastDay, $employees);
                $leaveDays = $this->countLeaveSecondsInMonth($firstDay, $lastDay, $employees);
                $months[] = new CompanyView(
                    $possibleSecondsInMoth,
                    $plannedSecondsInMonth,
                    $billableSecondsInMonth,
                    $noBillableSecondsInMonth + $leaveDays
                );
            }
        }
        return $months;
    }

    public function getYears(): array
    {
        $result  = $this->connection->fetchAssoc(
            'SELECT EXTRACT(year from MIN(hired_at)) as min_year,
                        EXTRACT(year from (IF(MAX(hired_at) > MAX(hired_to), MAX(hired_at), MAX(hired_to)))) as max_year
                        from employee;'
        );

        return range($result['min_year'], $result['max_year']);
    }


    public function getTribes(): array
    {
        return $this->connection->fetchAll('
            select id, name, extract(year from created_at) as year from tribe;
       ');
    }

    public function getEmployeesByDateAndTribes(DateTime $time, array $tribes): array
    {
        $firstDay = clone $time;
        $lastDay = $time->modify('last day of this month 23:59:59');
        $numberWorkDays = $this->countWorkDaysInMonth($firstDay, $lastDay);
        $qb = $this->em->createQueryBuilder();
        $qb->select('e.id, CONCAT(e.name, \' \', e.lastName) as full_name')
            ->addSelect('(e.jobTimeValue * :work_days / 60 / 60) as work_hours')
            ->from('AdventureBundle:Employee', 'e')
            ->where('e.tribe IN (:tribes)')
            ->andWhere('e.hiredAt <= :stopDate')
            ->andWhere('e.hiredTo >= :startDate OR e.hiredTo IS NULL')
            ->orderBy('e.lastName')
            ->setParameter('work_days', $numberWorkDays)
            ->setParameter('startDate', $firstDay->format('Y-m-d'))
            ->setParameter('stopDate', $lastDay->format('Y-m-d'))
            ->setParameter('tribes', $tribes, Connection::PARAM_INT_ARRAY);
        $result = $qb->getQuery()->getResult();

        return array_map(function (array $record) use ($firstDay, $lastDay) {
            return new CompanyPerEmployeeView(
                $record['id'],
                $record['full_name'],
                $this->possibleHoursInMonth($firstDay, $lastDay, [$record['id']]),
                $this->plannedHoursInMonth($firstDay, $lastDay, [$record['id']])
                + $this->countLeaveHoursInMonth($firstDay, $lastDay, [$record['id']]),
                $this->billableOrNoBillableHoursInMonth($firstDay, $lastDay, [$record['id']], true),
                $this->billableOrNoBillableHoursInMonth($firstDay, $lastDay, [$record['id']], false)
                + $this->countLeaveHoursInMonth($firstDay, $lastDay, [$record['id']])
            );
        }, $result);
    }

    private function countFreeDays(DateTime $firstDayOfMonth, DateTime $lastDayOfMonth) :int
    {
        $freeDays = $this->supplier->getFreeDays(
            (int)$firstDayOfMonth->format('Y'),
            (int)$lastDayOfMonth->format('Y')
        );
        $freeDaysInMonth = array_filter($freeDays, function ($day) use ($firstDayOfMonth) {
            $day = new DateTime($day);
            if ($day->format('m') !== $firstDayOfMonth->format('m')) {
                return false;
            }
            $numberDay = (int)$day->format('w');
            if ($numberDay === 6) {
                return false;
            }
            if ($numberDay === 0) {
                return false;
            }
            return true;
        });

        return count($freeDaysInMonth);
    }

    protected function getEmployees(DateTime $firstDay, DateTime $lastDay, array $tribes): array
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('e.id')
           ->from('AdventureBundle:Employee', 'e')
           ->where('e.tribe IN (:tribes)')
            ->andWhere('e.hiredAt <= :stop')
            ->andWhere('e.hiredTo >= :start OR e.hiredTo IS NULL')
            ->setParameter('start', $firstDay->format('Y-m-d'))
            ->setParameter('stop', $lastDay->format('Y-m-d'))
            ->setParameter('tribes', $tribes, Connection::PARAM_INT_ARRAY);
        $result = $qb->getQuery()->getResult();
        return array_map(fn(array $record) :int  => $record['id'], $result);
    }

    protected function possibleSecondsInMonth(DateTime $firstDay, DateTime $lastDay, array $employees): int
    {

        $numberWorkDays = $this->countWorkDaysInMonth($firstDay, $lastDay);
        $qb = $this->em->createQueryBuilder();
        $qb->select('SUM(e.jobTimeValue * :number_work_days) as seconds_in_month')
            ->from('AdventureBundle:Employee', 'e')->where('e.id IN (:employees)')
            ->setParameter('number_work_days', $numberWorkDays)
            ->setParameter('employees', $employees, Connection::PARAM_INT_ARRAY);
        return (int)$qb->getQuery()->getSingleScalarResult();
    }

    protected function billableOrNoBillableSecondsInMonth(
        DateTime $firstDay,
        DateTime $lastDay,
        array $employees,
        bool $billable = true
    ) :int {
        $qb = $this->em->createQueryBuilder();
        $qb->select('SUM(eo.occupancy)')
            ->from('AdventureBundle:EmployeeOccupancy', 'eo')
            ->innerJoin('eo.employee', 'employee')
            ->innerJoin('eo.project', 'project')
            ->where('employee.id IN (:employees)')
            ->andWhere('eo.date >= :start')
            ->andWhere('eo.date <= :stop')
            ->andWhere('project.billable = :billable')
            ->setParameter('employees', $employees, Connection::PARAM_INT_ARRAY)
            ->setParameter('start', $firstDay->getTimestamp())
            ->setParameter('stop', $lastDay->getTimestamp())
            ->setParameter('billable', (int)$billable);
        return (int)$qb->getQuery()->getSingleScalarResult();
    }

    protected function plannedSecondsInMonth(
        DateTime $firstDay,
        DateTime $lastDay,
        array $employees
    ) :int {
        $qb = $this->em->createQueryBuilder();
        $qb->select('SUM(eo.occupancy)')
            ->from('AdventureBundle:EmployeeOccupancy', 'eo')
            ->innerJoin('eo.employee', 'employee')
            ->where('employee.id IN (:employees)')
            ->andWhere('eo.date >= :start')
            ->andWhere('eo.date <= :stop')
            ->setParameter('employees', $employees, Connection::PARAM_INT_ARRAY)
            ->setParameter('start', $firstDay->getTimestamp())
            ->setParameter('stop', $lastDay->getTimestamp());
        return (int)$qb->getQuery()->getSingleScalarResult();
    }

    protected function countWorkDaysInMonth(DateTime $firstDay, DateTime $lastDay) :int
    {
        $numberDayInMonth = $firstDay->format('t');
        $numberSaturdayAndSundayDays = 0;
        $interval = new DateInterval('P1D');
        $daterRange = new DatePeriod($firstDay, $interval, $lastDay);

        foreach ($daterRange as $day) {
            $numberDay = (int)$day->format('w');
            if ($numberDay === 6 || $numberDay === 0) {
                $numberSaturdayAndSundayDays ++;
            }
        }
        $numberFreeDays = $this->countFreeDays($firstDay, $lastDay);
        return (int)$numberDayInMonth - (int)$numberFreeDays - (int)$numberSaturdayAndSundayDays;
    }

    protected function countLeaveSecondsInMonth(DateTime $firstDay, DateTime $lastDay, array $employees) :int
    {
        return $this->countLeaveInMonth($firstDay, $lastDay, $employees) * 3600;
    }

    protected function possibleHoursInMonth(
        \DateTime $firstDay,
        \DateTime $lastDay,
        array $employees
    ) :int {
        $seconds = $this->possibleSecondsInMonth($firstDay, $lastDay, $employees);
        if ($seconds === 0) {
            return 0;
        }
        return $seconds/3600;
    }

    protected function plannedHoursInMonth(\DateTime $firstDay, \DateTime $lastDay, array $employees) :int
    {
        $seconds = $this->plannedSecondsInMonth($firstDay, $lastDay, $employees);
        if ($seconds === 0) {
            return 0;
        }
        return $seconds/3600;
    }

    protected function countLeaveHoursInMonth(\DateTime $firstDay, \DateTime $lastDay, array $employees) :int
    {
        return $this->countLeaveInMonth($firstDay, $lastDay, $employees) * 8;
    }

    protected function countLeaveInMonth(\DateTime $firstDay, \DateTime $lastDay, array $employees) :int
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('COUNT(lrd.hours)')
           ->from('AdventureBundle:LeaveRequestDay', 'lrd')
           ->where('lrd.employee IN (:employees)')
           ->andWhere('lrd.date >= :start')
           ->andWhere('lrd.date <= :stop')
           ->andWhere('lrd.status IN (0,2)')
           ->andWhere('request.status = 1')
           ->innerJoin('lrd.request', 'request')
           ->setParameter('start', $firstDay->format('Y-m-d'))
           ->setParameter('stop', $lastDay->format('Y-m-d'))
           ->setParameter('employees', $employees, Connection::PARAM_INT_ARRAY);
        return (int)$qb->getQuery()->getSingleScalarResult();
    }

    protected function billableOrNoBillableHoursInMonth(
        \DateTime $firstDay,
        \DateTime $lastDay,
        array $employees,
        bool $billable = true
    ) :int {
        $seconds = $this->billableOrNoBillableSecondsInMonth($firstDay, $lastDay, $employees, $billable);
        if ($seconds === 0) {
            return 0;
        }
        return $seconds/3600;
    }
}
