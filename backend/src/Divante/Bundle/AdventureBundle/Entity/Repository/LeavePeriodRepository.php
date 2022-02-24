<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 08.02.19
 * Time: 07:34
 */

namespace Divante\Bundle\AdventureBundle\Entity\Repository;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\LeavePeriod;
use Divante\Bundle\AdventureBundle\Entity\LeaveRequest;
use Divante\Bundle\AdventureBundle\Entity\LeaveRequestDay;
use Doctrine\ORM\EntityRepository;

class LeavePeriodRepository extends EntityRepository
{
    /**
     * Get today expiring periods from employees with special day types (i.e contract CoE)
     * and transfer not used leaving days to existing next period.
     *
     * @return LeavePeriod[]
     * @throws \Exception
     */
    public function daysTransfer() : array
    {
        $daysTransfer = [];
        $todayPeriods = $this->queryTodayPeriods();
        if (empty($todayPeriods)) {
            return [];
        }

        /** @var LeavePeriod $period */
        foreach ($todayPeriods as $period) {
            $freeDaysAvailable = $period->getFreedays();
            $freeDaysUsedCount = $this->queryFreedaysused($period);
            $freeDaysNotUsed = $freeDaysAvailable - $freeDaysUsedCount;
            if ($freeDaysNotUsed > 0) {
                try {
                    // Get next period
                    /** @var LeavePeriod $nextPeriod */
                    $nextPeriod = $this->queryNextPeriod($period);
                    $period->setCommentSystem(
                        sprintf(
                            'To period from %s to %s was transfer %d %s.',
                            $nextPeriod->getDateFrom()->format('Y-m-d'),
                            $nextPeriod->getDateTo()->format('Y-m-d'),
                            $freeDaysNotUsed,
                            $this->formatDayText($freeDaysNotUsed)
                        )
                    );
                    // Transfer from old period to new period not used days
                    $em = $this->getEntityManager();
                    $period->setFreedays($period->getFreedays() - $freeDaysNotUsed);
                    $nextPeriod->setFreedays($nextPeriod->getFreedays() + $freeDaysNotUsed);
                    $em->persist($period);
                    $em->persist($nextPeriod);
                    $em->flush();

                    $daysTransfer[] = $period;
                    $daysTransfer[] = $nextPeriod;
                } catch (\Exception $exception) {
                    continue;
                }
            }
        }

        return $daysTransfer;
    }

    /**
     * @param int $qty
     * @return string
     */
    private function formatDayText(int $qty)
    {
        return $qty === 1 ? 'day' : 'days';
    }

    /**
     * Get today expiring periods (not processed) from employees with special day types.
     *
     * @return LeavePeriod[]
     * @throws \Exception
     */
    private function queryTodayPeriods() : array
    {
        $date = new \DateTime();
        $dateNow = $date->format('Y-m-d');

        // Get today periods
        $qb = $this->createQueryBuilder('p');
        $qb->andWhere(
            $qb->expr()->eq('p.dateTo', ':dateNow')
        );
        $qb->setParameter('dateNow', $dateNow);
        $qb->andWhere(
            $qb->expr()->isNull('p.commentSystem')
        );
        // Join with employee
        $qb->innerJoin(
            'AdventureBundle:Employee',
            'e',
            \Doctrine\ORM\Query\Expr\Join::WITH,
            'e.id = p.employee'
        );
        $qb->andWhere(
            $qb->expr()->in(
                'e.contractId',
                [
                    Employee::CONTRACT_B2B_LUMP_SUM,
                    Employee::CONTRACT_B2B_HOURLY,
                    Employee::CONTRACT_CLC_LUMP_SUM,
                    Employee::CONTRACT_CLC_HOURLY
                ]
            )
        );

        $result = $qb->getQuery()->getResult();

        return $result;
    }

    /**
     * Get next employee period using current one.
     *
     * @param LeavePeriod $currentPeriod
     * @return LeavePeriod
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function queryNextPeriod(LeavePeriod $currentPeriod) : LeavePeriod
    {
        $qb = $this->createQueryBuilder('p');
        $qb->andWhere(
            $qb->expr()->gt('p.dateFrom', ':dateTo')
        );
        $qb->setParameter('dateTo', $currentPeriod->getDateTo()->format('Y-m-d'));
        $qb->andWhere(
            $qb->expr()->eq('p.employee', ':employee')
        );
        $qb->setParameter('employee', $currentPeriod->getEmployee());

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Number of used free days (DAY_TYPE_FREE_PAID, DAY_TYPE_LEAVE_PAID, DAY_TYPE_LEAVE_REQUEST)
     *
     * @param LeavePeriod $leavePeriod
     * @return int
     */
    private function queryFreedaysused(LeavePeriod $leavePeriod) : int
    {
        /** @var EntityRepository $repoRequest */
        $repoRequest = $this->getEntityManager()->getRepository(LeaveRequest::class);
        /** @var EntityRepository $repoDays */
        $repoDays = $this->getEntityManager()->getRepository(LeaveRequestDay::class);

        $acceptedStatuses = [
            LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION,
            LeaveRequest::REQUEST_STATUS_ACCEPTED,
        ];

        $acceptedTypes = [
            LeaveRequestDay::DAY_TYPE_FREE_PAID,
            LeaveRequestDay::DAY_TYPE_LEAVE_PAID,
            LeaveRequestDay::DAY_TYPE_LEAVE_REQUEST,
        ];

        $requests = $repoRequest->findBy([
            'period' => $leavePeriod,
            'status' => $acceptedStatuses,
        ]);

        $freeDaysUsed = $repoDays->count([
            'request' => $requests,
            'status' => LeaveRequestDay::DAY_STATUS_ACTIVE,
            'type' => $acceptedTypes
        ]);
        return $freeDaysUsed;
    }

    /**
     * Prepare free days report
     *
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function freeDaysReport() : array
    {
        $sql = '
        SELECT 
            CONCAT(e.last_name, \' \', e.name) AS "employeeName", 
            CASE e.contract_id
                WHEN '.Employee::CONTRACT_B2B_LUMP_SUM.' THEN "B2B LUMP SUM"
                WHEN '.Employee::CONTRACT_B2B_HOURLY.' THEN "B2B HOURLY"
                WHEN '.Employee::CONTRACT_CLC_LUMP_SUM.' THEN "CLC LUMP SUM"
                WHEN '.Employee::CONTRACT_CLC_HOURLY.' THEN "CLC HOURLY"
                WHEN '.Employee::CONTRACT_COE.' THEN "CoE"
            END AS "contractName", 
            edp.date_from AS "periodFrom", 
            edp.date_to AS "periodTo", 
            edp.freedays AS "freedaysOwed",
        (
            SELECT COUNT(date) as pl
            FROM employee_daysoff_request_day edrd 
            LEFT JOIN employee_daysoff_request edr ON edr.id = edrd.request_id
            WHERE edrd.employee_id = e.id 
                AND edrd.period_id = edp.id 
                AND (edr.status = '.LeaveRequest::REQUEST_STATUS_ACCEPTED.'
                    OR edr.status = '.LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION.')
                AND edrd.status = '.LeaveRequestDay::DAY_STATUS_ACTIVE.' 
                AND (edrd.type = '.LeaveRequestDay::DAY_TYPE_FREE_PAID.'
                    OR edrd.type = '.LeaveRequestDay::DAY_TYPE_LEAVE_PAID.'
                    OR edrd.type = '.LeaveRequestDay::DAY_TYPE_LEAVE_REQUEST.')       
            ORDER BY date ASC
        ) AS "freedaysPaidUsed",
        (
            SELECT COUNT(date) 
            FROM employee_daysoff_request_day edrd 
            LEFT JOIN employee_daysoff_request edr ON edr.id = edrd.request_id
            WHERE edrd.employee_id = e.id 
                AND edrd.period_id = edp.id 
                AND (edr.status = '.LeaveRequest::REQUEST_STATUS_ACCEPTED.'
                    OR edr.status = '.LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION.')
                AND edrd.status = '.LeaveRequestDay::DAY_STATUS_ACTIVE.'
                AND (edrd.type = '.LeaveRequestDay::DAY_TYPE_FREE_UNPAID.'
                    OR edrd.type = '.LeaveRequestDay::DAY_TYPE_LEAVE_UNPAID.'
                    OR edrd.type = '.LeaveRequestDay::DAY_TYPE_UNAVAILABILITY.')
            ORDER BY date ASC
        ) AS "freedaysUnpaidUsed",
        (
            SELECT COUNT(date) 
            FROM employee_daysoff_request_day edrd 
            LEFT JOIN employee_daysoff_request edr ON edr.id = edrd.request_id
            WHERE edrd.employee_id = e.id 
                AND edrd.period_id = edp.id 
                AND (edr.status = '.LeaveRequest::REQUEST_STATUS_ACCEPTED.'
                    OR edr.status = '.LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION.')
                AND edrd.status = '.LeaveRequestDay::DAY_STATUS_ACTIVE.'
                AND edrd.type = '.LeaveRequestDay::DAY_TYPE_LEAVE_REQUEST.'                 
            ORDER BY date ASC
        ) AS "freedaysRequest",
        (
            SELECT COUNT(date) 
            FROM employee_daysoff_request_day edrd 
            LEFT JOIN employee_daysoff_request edr ON edr.id = edrd.request_id
            WHERE edrd.employee_id = e.id 
                AND edrd.period_id = edp.id 
                AND (edr.status = '.LeaveRequest::REQUEST_STATUS_ACCEPTED.'
                    OR edr.status = '.LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION.')
                AND edrd.status = '.LeaveRequestDay::DAY_STATUS_ACTIVE.'
                AND edrd.type = '.LeaveRequestDay::DAY_TYPE_LEAVE_OCCASIONAL.'                 
            ORDER BY date ASC
        ) AS "freedaysOccasional",
        (
            SELECT COUNT(date) 
            FROM employee_daysoff_request_day edrd 
            LEFT JOIN employee_daysoff_request edr ON edr.id = edrd.request_id
            WHERE edrd.employee_id = e.id 
                AND edrd.period_id = edp.id 
                AND (edr.status = '.LeaveRequest::REQUEST_STATUS_ACCEPTED.'
                    OR edr.status = '.LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION.')
                AND edrd.status = '.LeaveRequestDay::DAY_STATUS_ACTIVE.'
                AND edrd.type = '.LeaveRequestDay::DAY_TYPE_LEAVE_CARE.'                 
            ORDER BY date ASC
        ) AS "freedaysCare",
        (
            SELECT GROUP_CONCAT(date) 
            FROM employee_daysoff_request_day edrd 
            LEFT JOIN employee_daysoff_request edr ON edr.id = edrd.request_id
            WHERE edrd.employee_id = e.id
                AND edrd.period_id = edp.id  
                AND (edr.status = '.LeaveRequest::REQUEST_STATUS_ACCEPTED.'
                    OR edr.status = '.LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION.')
                AND edrd.status = '.LeaveRequestDay::DAY_STATUS_ACTIVE.'
                AND (edrd.type = '.LeaveRequestDay::DAY_TYPE_FREE_PAID.'
                    OR edrd.type = '.LeaveRequestDay::DAY_TYPE_LEAVE_PAID.'
                    OR edrd.type = '.LeaveRequestDay::DAY_TYPE_LEAVE_REQUEST.') 
            ORDER BY date ASC
        ) AS "freedaysPaidList",
        (
            SELECT GROUP_CONCAT(date) 
            FROM employee_daysoff_request_day edrd 
            LEFT JOIN employee_daysoff_request edr ON edr.id = edrd.request_id
            WHERE edrd.employee_id = e.id 
                AND edrd.period_id = edp.id 
                AND (edr.status = '.LeaveRequest::REQUEST_STATUS_ACCEPTED.'
                    OR edr.status = '.LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION.')
                AND edrd.status = '.LeaveRequestDay::DAY_STATUS_ACTIVE.'
                AND (edrd.type = '.LeaveRequestDay::DAY_TYPE_FREE_UNPAID.'
                    OR edrd.type = '.LeaveRequestDay::DAY_TYPE_LEAVE_UNPAID.'
                    OR edrd.type = '.LeaveRequestDay::DAY_TYPE_UNAVAILABILITY.')
            ORDER BY date ASC
        ) AS "freedaysUnpaidList",
        (
            SELECT GROUP_CONCAT(date) 
            FROM employee_daysoff_request_day edrd 
            LEFT JOIN employee_daysoff_request edr ON edr.id = edrd.request_id
            WHERE edrd.employee_id = e.id 
                AND edrd.period_id = edp.id 
                AND (edr.status = '.LeaveRequest::REQUEST_STATUS_ACCEPTED.'
                    OR edr.status = '.LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION.')
                AND edrd.status = '.LeaveRequestDay::DAY_STATUS_ACTIVE.'
                AND edrd.type = '.LeaveRequestDay::DAY_TYPE_LEAVE_REQUEST.'                 
            ORDER BY date ASC
        ) AS "freedaysRequestList",
        (
            SELECT GROUP_CONCAT(date) 
            FROM employee_daysoff_request_day edrd 
            LEFT JOIN employee_daysoff_request edr ON edr.id = edrd.request_id
            WHERE edrd.employee_id = e.id 
                AND edrd.period_id = edp.id 
                AND (edr.status = '.LeaveRequest::REQUEST_STATUS_ACCEPTED.'
                    OR edr.status = '.LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION.')
                AND edrd.status = '.LeaveRequestDay::DAY_STATUS_ACTIVE.'
                AND edrd.type = '.LeaveRequestDay::DAY_TYPE_LEAVE_OCCASIONAL.'                 
            ORDER BY date ASC
        ) AS "freedaysOccasionalList",
        (
            SELECT GROUP_CONCAT(date) 
            FROM employee_daysoff_request_day edrd 
            LEFT JOIN employee_daysoff_request edr ON edr.id = edrd.request_id
            WHERE edrd.employee_id = e.id 
                AND edrd.period_id = edp.id 
                AND (edr.status = '.LeaveRequest::REQUEST_STATUS_ACCEPTED.'
                    OR edr.status = '.LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION.')
                AND edrd.status = '.LeaveRequestDay::DAY_STATUS_ACTIVE.'
                AND edrd.type = '.LeaveRequestDay::DAY_TYPE_LEAVE_CARE.'                 
            ORDER BY date ASC
        ) AS "freedaysCareList",
        (
            SELECT GROUP_CONCAT(date)
            FROM employee_sick_leave_request_day eslrd
            LEFT JOIN employee_sick_leave_request eslr ON eslr.id = eslrd.request_id
            WHERE eslrd.employee_id = e.id
                AND eslrd.period_id = eslp.id
                AND (eslr.status = '.LeaveRequest::REQUEST_STATUS_ACCEPTED.'
                    OR eslr.status = '.LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION.')
                AND eslrd.status = '.LeaveRequestDay::DAY_STATUS_ACTIVE.'
                AND (eslrd.type = '.LeaveRequestDay::DAY_TYPE_SICK_LEAVE_PAID.')
            ORDER BY date ASC
        ) AS "sickLeaveDaysList",
        eslp.sick_leave_days AS "sickleavedaysOwed",
        (
            SELECT COUNT(date) as pl
            FROM employee_sick_leave_request_day eslrd
            LEFT JOIN employee_sick_leave_request eslr ON eslr.id = eslrd.request_id
            WHERE eslrd.employee_id = e.id
                AND eslrd.period_id = eslp.id
                AND (eslr.status = '.LeaveRequest::REQUEST_STATUS_ACCEPTED.'
                    OR eslr.status = '.LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION.')
                AND eslrd.status = '.LeaveRequestDay::DAY_STATUS_ACTIVE.'
                AND (eslrd.type = '.LeaveRequestDay::DAY_TYPE_SICK_LEAVE_PAID.')
            ORDER BY date ASC
        ) AS "sickLeaveDaysUsed"
        FROM employee AS e 
        INNER JOIN employee_daysoff_period AS edp ON edp.employee_id = e.id
        INNER JOIN employee_sick_leave_period AS eslp ON eslp.employee_id = e.id
        WHERE edp.date_from = eslp.date_from
        ORDER BY e.last_name ASC, e.name ASC 
        ';

        $reportRows
            = $this
            ->getEntityManager()
            ->getConnection()
            ->executeQuery($sql)
            ->fetchAll();

        return $reportRows;
    }

    /**
     * Get expiring in given month or non existing employee periods.
     *
     * @param int $expiringIn Expiring in given month number
     * @return mixed[]
     * @throws \Exception
     */
    public function expiringPeriodReport(int $expiringIn = 1) : array
    {
        $expiringIn -= 1;
        $expiringIn *= -1;

        $sql = '
        SELECT 
            e.id as "employeeId",
            CASE e.contract_id
                WHEN '.Employee::CONTRACT_B2B_LUMP_SUM.' THEN "B2B LUMP SUM"
                WHEN '.Employee::CONTRACT_B2B_HOURLY.' THEN "B2B HOURLY"
                WHEN '.Employee::CONTRACT_CLC_LUMP_SUM.' THEN "CLC LUMP SUM"
                WHEN '.Employee::CONTRACT_CLC_HOURLY.' THEN "CLC HOURLY"
                WHEN '.Employee::CONTRACT_COE.' THEN "CoE"
            END AS "contractName", 
            CONCAT(e.last_name, \' \', e.name) AS "employeeName",
            lp1.id as "periodId",
            lp1.date_from as "dateFrom", 
            lp1.date_to as "dateTo"
        FROM leave_period lp1
        INNER JOIN (
            SELECT employee_id, max(date_from) MaxDateFrom, max(date_to) MaxDateTo
            FROM leave_period
            GROUP BY employee_id
        ) lp2
              ON  lp1.employee_id = lp2.employee_id
              AND lp1.date_from = lp2.MaxDateFrom
        RIGHT JOIN employee e ON e.id = lp1.employee_id
        WHERE 
	      (lp1.date_from IS NULL OR PERIOD_DIFF(EXTRACT(YEAR_MONTH FROM NOW()), 
	      EXTRACT(YEAR_MONTH FROM lp1.date_to)) >= '.$expiringIn.')
	      AND
	      e.hired_to is NULL
        ORDER BY lp1.date_to ASC
        ';

        $reportRows
            = $this
            ->getEntityManager()
            ->getConnection()
            ->executeQuery($sql)
            ->fetchAll();

        return $reportRows;
    }
}
