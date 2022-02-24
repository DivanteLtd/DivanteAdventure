<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 13.02.19
 * Time: 11:55
 */

namespace Divante\Bundle\AdventureBundle\Message\Leaves;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class UpdatePeriod
{
    use ObjectTrait;

    private int $periodId;
    private ?string $dateFrom;
    private ?string $dateTo;
    private ?int $sickLeaveDays;
    private ?int $freeDays;
    private ?int $employeeId;
    private ?string $comment;

    public function __construct(
        int $periodId,
        ?string $dateFrom,
        ?string $dateTo,
        ?int $sickLeaveDays,
        ?int $freeDays,
        ?int $employeeId,
        ?string $comment
    ) {
        $this->periodId = $periodId;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->sickLeaveDays = $sickLeaveDays;
        $this->freeDays = $freeDays;
        $this->employeeId = $employeeId;
        $this->comment = $comment;
    }

    public function getPeriodId() : int
    {
        return $this->periodId;
    }

    public function getDateFrom() : ?string
    {
        return $this->dateFrom;
    }

    public function getDateTo() : ?string
    {
        return $this->dateTo;
    }

    public function getSickLeaveDays() : ?int
    {
        return $this->sickLeaveDays;
    }

    public function getFreeDays() : ?int
    {
        return $this->freeDays;
    }

    public function getEmployeeId() : ?int
    {
        return $this->employeeId;
    }

    public function getComment() : ?string
    {
        return $this->comment;
    }
}
