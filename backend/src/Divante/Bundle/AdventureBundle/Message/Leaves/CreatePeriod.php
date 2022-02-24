<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 13.02.19
 * Time: 10:30
 */

namespace Divante\Bundle\AdventureBundle\Message\Leaves;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class CreatePeriod
{
    use ObjectTrait;

    private string $dateFrom;
    private string $dateTo;
    private int $sickLeaveDays;
    private int $freeDays;
    private int $employeeId;

    public function __construct(
        string $dateFrom,
        string $dateTo,
        int $sickLeaveDays,
        int $freeDays,
        int $employeeId
    ) {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->sickLeaveDays = $sickLeaveDays;
        $this->freeDays = $freeDays;
        $this->employeeId = $employeeId;
    }

    public function getDateFrom() : string
    {
        return $this->dateFrom;
    }

    public function getDateTo() : string
    {
        return $this->dateTo;
    }

    public function getSickLeaveDays() : int
    {
        return $this->sickLeaveDays;
    }

    public function getFreeDays() : int
    {
        return $this->freeDays;
    }

    public function getEmployeeId() : int
    {
        return $this->employeeId;
    }
}
