<?php

namespace Divante\Bundle\AdventureBundle\Entity\Repository\WorkLocation;

use Divante\Bundle\AdventureBundle\Entity\EmployeeWorkLocation;
use Doctrine\ORM\EntityRepository;

class WorkLocationRepository extends EntityRepository
{
     /**
     * Get records with date < (today - 45 days)
     * @return EmployeeWorkLocation[]
     */
    public function queryOldDates() : array
    {
        $monthAndHalfAgo = (new \DateTime())->modify('-45 day');
        return $this->createQueryBuilder('ewl')
            ->where('ewl.date < :date')->setParameter('date', $monthAndHalfAgo->setTime(23, 59, 59))
            ->getQuery()
            ->getResult();
    }

     /**
     * Get records for last month
     * @return EmployeeWorkLocation[]
     */
    public function queryLastMonthDates() : array
    {
        $lastMonth = (new \DateTime())->modify('-1 month');
        return $this->createQueryBuilder('ewl')
            ->where('ewl.date >= :date')->setParameter('date', $lastMonth->setTime(0, 0, 0))
            ->getQuery()
            ->getResult();
    }

     /**
     * Get records for employee with date >= today
     * @param int $employeeId
     * @param int $type
     * @return EmployeeWorkLocation[]
     */
    public function queryEmployeeDates(int $employeeId, int $type) : array
    {
        $today = new \DateTime();
        return $this->createQueryBuilder('ewl')
            ->where('ewl.date >= :date')->setParameter('date', $today->setTime(0, 0, 0))
            ->andWhere('ewl.employeeId = :employeeId')->setParameter('employeeId', $employeeId)
            ->andWhere('ewl.type = :type')->setParameter('type', $type)
            ->getQuery()
            ->getResult();
    }

    /**
     * Get records for employees with date = today
     * @return EmployeeWorkLocation[]
     */
    public function queryEmployeeTodayRecord() : array
    {
        $today = new \DateTime();
        return $this->createQueryBuilder('ewl')
            ->where('ewl.date = :date')->setParameter('date', $today->setTime(0, 0, 0))
            ->getQuery()
            ->getResult();
    }
}
