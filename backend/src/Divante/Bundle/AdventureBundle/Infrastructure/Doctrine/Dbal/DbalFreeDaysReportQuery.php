<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 * Date: 29.04.19
 * Time: 12:25
 */

namespace Divante\Bundle\AdventureBundle\Infrastructure\Doctrine\Dbal;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\LeaveRequest;
use Divante\Bundle\AdventureBundle\Entity\LeaveRequestDay;
use Divante\Bundle\AdventureBundle\Query\FreeDays\FreeDaysReportQuery;
use Doctrine\DBAL\Connection;

class DbalFreeDaysReportQuery implements FreeDaysReportQuery
{
    protected Connection $conn;

    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    /** @inheritdoc */
    public function getReport(): array
    {
        return $this->conn->fetchAll(
            'SELECT 
            CONCAT(e.last_name, " ", e.name) AS "employeeName",
           CASE e.contract_id
                    WHEN '.Employee::CONTRACT_B2B_LUMP_SUM.' THEN "B2B LUMP SUM"
                    WHEN '.Employee::CONTRACT_B2B_HOURLY.' THEN "B2B HOURLY"
                    WHEN '.Employee::CONTRACT_CLC_LUMP_SUM.' THEN "CLC LUMP SUM"
                    WHEN '.Employee::CONTRACT_CLC_HOURLY.' THEN "CLC HOURLY"
                    WHEN '.Employee::CONTRACT_COE.' THEN "CoE"
                END AS "contractName", 
            lp.date_from AS "periodFrom", 
            lp.date_to AS "periodTo", 
            lp.freedays AS "freedaysOwed",
        (
            SELECT COUNT(date) as pl
            FROM leave_request_day lrd 
            LEFT JOIN leave_request lr ON lr.id = lrd.request_id
            WHERE lrd.employee_id = e.id
                AND lrd.period_id = lp.id
                AND (lr.status = '.LeaveRequest::REQUEST_STATUS_ACCEPTED.'
                OR lr.status = '.LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION.')
                AND lrd.status = '.LeaveRequestDay::DAY_STATUS_ACTIVE.'
                AND (lrd.type = '.LeaveRequestDay::DAY_TYPE_FREE_PAID.'
                OR lrd.type = '.LeaveRequestDay::DAY_TYPE_LEAVE_PAID.'
                OR lrd.type = '.LeaveRequestDay::DAY_TYPE_LEAVE_REQUEST.')       
            ORDER BY date ASC
        ) AS "freeDaysPaidUsed",
        (
            SELECT COUNT(date) 
            FROM leave_request_day lrd 
            LEFT JOIN leave_request lr ON lr.id = lrd.request_id
            WHERE lrd.employee_id = e.id 
                AND lrd.period_id = lp.id 
                AND (lr.status = '.LeaveRequest::REQUEST_STATUS_ACCEPTED.' 
                OR lr.status = '.LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION.')
                AND lrd.status = '.LeaveRequestDay::DAY_STATUS_ACTIVE.'
                AND (lrd.type = '.LeaveRequestDay::DAY_TYPE_FREE_UNPAID.' 
                OR lrd.type = '.LeaveRequestDay::DAY_TYPE_LEAVE_UNPAID.'
                OR lrd.type = '.LeaveRequestDay::DAY_TYPE_UNAVAILABILITY.')                 
            ORDER BY date ASC
        ) AS "freeDaysUnpaidUsed",
        (
            SELECT COUNT(date) 
            FROM leave_request_day lrd 
            LEFT JOIN leave_request lr ON lr.id = lrd.request_id
            WHERE lrd.employee_id = e.id 
                AND lrd.period_id = lp.id 
                AND (lr.status = '.LeaveRequest::REQUEST_STATUS_ACCEPTED.' 
                OR lr.status = '.LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION.')
                AND lrd.status = '.LeaveRequestDay::DAY_STATUS_ACTIVE.'
                AND lrd.type = '.LeaveRequestDay::DAY_TYPE_LEAVE_REQUEST.'                 
            ORDER BY date ASC
        ) AS "freeDaysRequest",
        (
            SELECT COUNT(date) 
            FROM leave_request_day lrd 
            LEFT JOIN leave_request lr ON lr.id = lrd.request_id
            WHERE lrd.employee_id = e.id 
                AND lrd.period_id = lp.id 
                AND (lr.status = '.LeaveRequest::REQUEST_STATUS_ACCEPTED.' 
                OR lr.status = '.LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION.')
                AND lrd.status = '.LeaveRequestDay::DAY_STATUS_ACTIVE.'
                AND lrd.type = '.LeaveRequestDay::DAY_TYPE_LEAVE_OCCASIONAL.'                 
            ORDER BY date ASC
        ) AS "freeDaysOccasional",
        (
            SELECT COUNT(date) 
            FROM leave_request_day lrd 
            LEFT JOIN leave_request lr ON lr.id = lrd.request_id
            WHERE lrd.employee_id = e.id 
                AND lrd.period_id = lp.id 
                AND (lr.status = '.LeaveRequest::REQUEST_STATUS_ACCEPTED.' 
                OR lr.status = '.LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION.')
                AND lrd.status = '.LeaveRequestDay::DAY_STATUS_ACTIVE.'
                AND lrd.type = '.LeaveRequestDay::DAY_TYPE_LEAVE_CARE.'                 
            ORDER BY date ASC
        ) AS "freeDaysCare",
        (
            SELECT GROUP_CONCAT(date) 
            FROM leave_request_day lrd 
            LEFT JOIN leave_request lr ON lr.id = lrd.request_id
            WHERE lrd.employee_id = e.id
                AND lrd.period_id = lp.id  
                AND (lr.status = '.LeaveRequest::REQUEST_STATUS_ACCEPTED.' 
                OR lr.status = '.LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION.')
                AND lrd.status = '.LeaveRequestDay::DAY_STATUS_ACTIVE.'
                AND (lrd.type = '.LeaveRequestDay::DAY_TYPE_FREE_PAID.' 
                OR lrd.type = '.LeaveRequestDay::DAY_TYPE_LEAVE_PAID.' 
                OR lrd.type = '.LeaveRequestDay::DAY_TYPE_LEAVE_REQUEST.') 
            ORDER BY date ASC
        ) AS "freeDaysPaidList",
        (
            SELECT GROUP_CONCAT(date) 
            FROM leave_request_day lrd 
            LEFT JOIN leave_request lr ON lr.id = lrd.request_id
            WHERE lrd.employee_id = e.id 
                AND lrd.period_id = lp.id 
                AND (lr.status = '.LeaveRequest::REQUEST_STATUS_ACCEPTED.' 
                OR lr.status = '.LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION.')
                AND lrd.status = '.LeaveRequestDay::DAY_STATUS_ACTIVE.'
                AND (lrd.type = '.LeaveRequestDay::DAY_TYPE_FREE_UNPAID.' 
                OR lrd.type = '.LeaveRequestDay::DAY_TYPE_LEAVE_UNPAID.'
                OR lrd.type = '.LeaveRequestDay::DAY_TYPE_UNAVAILABILITY.')             
            ORDER BY date ASC
        ) AS "freeDaysUnpaidList",
        (
            SELECT GROUP_CONCAT(date) 
            FROM leave_request_day lrd 
            LEFT JOIN leave_request lr ON lr.id = lrd.request_id
            WHERE lrd.employee_id = e.id 
                AND lrd.period_id = lp.id 
                AND (lr.status = '.LeaveRequest::REQUEST_STATUS_ACCEPTED.' 
                OR lr.status = '.LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION.')
                AND lrd.status = '.LeaveRequestDay::DAY_STATUS_ACTIVE.'
                AND lrd.type = '.LeaveRequestDay::DAY_TYPE_LEAVE_REQUEST.'                 
            ORDER BY date ASC
        ) AS "freeDaysRequestList",
        (
            SELECT GROUP_CONCAT(date) 
            FROM leave_request_day lrd 
            LEFT JOIN leave_request lr ON lr.id = lrd.request_id
            WHERE lrd.employee_id = e.id 
                AND lrd.period_id = lp.id 
                AND (lr.status = '.LeaveRequest::REQUEST_STATUS_ACCEPTED.' 
                OR lr.status = '.LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION.')
                AND lrd.status = '.LeaveRequestDay::DAY_STATUS_ACTIVE.'
                AND lrd.type = '.LeaveRequestDay::DAY_TYPE_LEAVE_OCCASIONAL.'                 
            ORDER BY date ASC
        ) AS "freeDaysOccasionalList",
        (
            SELECT GROUP_CONCAT(date) 
            FROM leave_request_day lrd 
            LEFT JOIN leave_request lr ON lr.id = lrd.request_id
            WHERE lrd.employee_id = e.id 
                AND lrd.period_id = lp.id 
                AND (lr.status = '.LeaveRequest::REQUEST_STATUS_ACCEPTED.' 
                OR lr.status = '.LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION.')
                AND lrd.status = '.LeaveRequestDay::DAY_STATUS_ACTIVE.'
                AND lrd.type = '.LeaveRequestDay::DAY_TYPE_LEAVE_CARE.'                 
            ORDER BY date ASC
        ) AS "freeDaysCareList",
        (
            SELECT GROUP_CONCAT(date)
            FROM leave_request_day lrd
            LEFT JOIN leave_request lr ON lr.id = lrd.request_id
            WHERE lrd.employee_id = e.id
                AND lrd.period_id = lp.id
                AND (lr.status = '.LeaveRequest::REQUEST_STATUS_ACCEPTED.' 
                OR lr.status = '.LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION.')
                AND lrd.status = '.LeaveRequestDay::DAY_STATUS_ACTIVE.'
                AND (lrd.type = '.LeaveRequestDay::DAY_TYPE_SICK_LEAVE_PAID.')
            ORDER BY date ASC
        ) AS "sickLeaveDaysList",
        lp.sick_leave_days AS "sickLeaveDaysOwed",
        (
            SELECT COUNT(date) as pl
            FROM leave_request_day lrd
            LEFT JOIN leave_request lr ON lr.id = lrd.request_id
            WHERE lrd.employee_id = e.id
                AND lrd.period_id = lp.id
                AND (lr.status = '.LeaveRequest::REQUEST_STATUS_ACCEPTED.' 
                OR lr.status = '.LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION.')
                AND lrd.status = '.LeaveRequestDay::DAY_STATUS_ACTIVE.'
                AND (lrd.type = '.LeaveRequestDay::DAY_TYPE_SICK_LEAVE_PAID.')
            ORDER BY date ASC
        ) AS "sickLeaveDaysUsed"
        FROM employee AS e 
        INNER JOIN leave_period AS lp ON lp.employee_id = e.id
        ORDER BY e.last_name ASC, e.name ASC'
        );
    }
}
