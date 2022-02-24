<?php

namespace Divante\Bundle\AdventureBundle\Query\Statistic;

class CompanyPerEmployeeView
{
    private int $id;
    private string $fullName;
    private int $workHours;
    private int $plannedHours;
    private int $billableHours;
    private int $noBillableHours;
    private float $ratio;

    public function __construct(
        int $id,
        string $fullName,
        ?int $workHours,
        ?int $plannedHours,
        ?int $billableHours,
        ?int $noBillableHours
    ) {
        $this->id              = $id;
        $this->fullName        = $fullName;
        $this->workHours       = (int)$workHours;
        $this->plannedHours    = (int)$plannedHours;
        $this->billableHours   = (int)$billableHours;
        $this->noBillableHours = (int)$noBillableHours;
        $this->ratio = 0;
        if ($this->plannedHours !== 0) {
            $this->ratio = round($this->billableHours/$this->plannedHours * 100);
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getWorkHours(): int
    {
        return $this->workHours;
    }

    public function getPlannedHours(): int
    {
        return $this->plannedHours;
    }

    public function getBillableHours(): int
    {
        return $this->billableHours;
    }

    public function getNoBillableHours(): int
    {
        return $this->noBillableHours;
    }

    public function getRatio(): float
    {
        return $this->ratio;
    }
}
