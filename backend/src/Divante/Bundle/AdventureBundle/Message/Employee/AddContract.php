<?php


namespace Divante\Bundle\AdventureBundle\Message\Employee;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class AddContract
{
    use ObjectTrait;

    protected int $typeId;
    protected int $employeeId;
    protected string $startDate;
    protected string $endDate;
    protected string $assignDate;
    protected ?string $noticePeriod;
    protected bool $active;

    public function __construct(
        int $typeId,
        int $employeeId,
        string $startDate,
        string $endDate,
        string $assignDate,
        ?string $noticePeriod,
        bool $active
    ) {
        $this->typeId = $typeId;
        $this->employeeId = $employeeId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->assignDate = $assignDate;
        $this->noticePeriod = $noticePeriod;
        $this->active = $active;
    }

    public function getTypeId(): int
    {
        return $this->typeId;
    }

    public function getEmployeeId(): int
    {
        return $this->employeeId;
    }

    public function getStartDate(): string
    {
        return $this->startDate;
    }

    public function getEndDate(): string
    {
        return $this->endDate;
    }

    public function getAssignDate(): string
    {
        return $this->assignDate;
    }

    public function getNoticePeriod(): ?string
    {
        return $this->noticePeriod;
    }

    public function isActive(): bool
    {
        return $this->active;
    }
}
