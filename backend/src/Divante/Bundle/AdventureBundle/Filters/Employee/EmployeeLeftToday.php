<?php

namespace Divante\Bundle\AdventureBundle\Filters\Employee;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\LeavePeriod;
use Divante\Bundle\AdventureBundle\Entity\LeaveRequest;
use Divante\Bundle\AdventureBundle\Entity\LeaveRequestDay;

/**
 * Filter EmployeeLeftToday returns "true" if and only if passed employee has accepted free day request today.
 * @package Divante\Bundle\AdventureBundle\Filters\Employee
 */
class EmployeeLeftToday
{
    /** @var array<int,array<string,bool>> */
    private static array $cache = [];

    public function __invoke(Employee $employee, ?\DateTime $date = null) : bool
    {
        if (!array_key_exists($employee->getId(), self::$cache)) {
            self::$cache[$employee->getId()] = [];
        }
        $currentDate = $date ?? new \DateTime();
        $currentDateFormatted = $currentDate->format('Y-m-d');
        if (array_key_exists($currentDateFormatted, self::$cache[$employee->getId()])) {
            return self::$cache[$employee->getId()][$currentDateFormatted];
        }
        /** @var LeavePeriod[] $periods */
        $periods = $employee->getPeriods()->toArray();
        /** @var LeavePeriod|null $currentPeriod */
        $currentPeriod = null;
        foreach ($periods as $period) {
            if ($period->getDateFrom() <= $currentDate && $period->getDateTo() >= $currentDate) {
                $currentPeriod = $period;
                break;
            }
        }
        if (is_null($currentPeriod)) {
            self::$cache[$employee->getId()][$currentDateFormatted] = false;
            return false;
        }

        $activeLeaveDayStatuses = [
            LeaveRequestDay::DAY_STATUS_ACTIVE,
            LeaveRequestDay::DAY_STATUS_PENDING_RESIGNATION,
        ];
        /** @var LeaveRequest[] $requests */
        $requests = $currentPeriod
            ->getRequests()
            ->filter(function (LeaveRequest $request) use ($currentDateFormatted, $activeLeaveDayStatuses) {
                $status = $request->getStatus();
                $accepted = LeaveRequest::REQUEST_STATUS_ACCEPTED;
                $resignationPending = LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION;
                if ($status !== $accepted && $status !== $resignationPending) {
                    return false;
                }
                /** @var LeaveRequestDay[] $days */
                $days = $request->getRequestDays()->toArray();
                foreach ($days as $leaveDay) {
                    $leaveDayDate = $leaveDay->getDate()->format('Y-m-d');
                    $leaveDayStatus = $leaveDay->getStatus();
                    if ($leaveDayDate === $currentDateFormatted && in_array($leaveDayStatus, $activeLeaveDayStatuses)) {
                        return true;
                    }
                }
                return false;
            })
            ->toArray();

        $requestExist = count($requests) > 0;
        self::$cache[$employee->getId()][$currentDateFormatted] = $requestExist;
        return $requestExist;
    }
}
