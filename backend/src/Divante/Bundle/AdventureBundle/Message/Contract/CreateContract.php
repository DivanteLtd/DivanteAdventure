<?php


namespace Divante\Bundle\AdventureBundle\Message\Contract;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class CreateContract
{
    use ObjectTrait;

    private int $typeId;
    private int $employeeId;
    private \DateTime $startDate;
    private \DateTime $endDate;
    private \DateTime $assignDate;
    private bool $active;

    public function __construct(
        int $typeId,
        int $employeeId,
        \DateTime $startDate,
        \DateTime $endDate,
        \DateTime $assignDate,
        bool $active
    ) {
        $this->typeId = $typeId;
        $this->employeeId = $employeeId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->assignDate = $assignDate;
        $this->active = $active;
    }

    /**
     * @return int
     */
    public function getTypeId(): int
    {
        return $this->typeId;
    }

    /**
     * @return int
     */
    public function getEmployeeId(): int
    {
        return $this->employeeId;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate(): \DateTime
    {
        return $this->startDate;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate(): \DateTime
    {
        return $this->endDate;
    }

    /**
     * @return \DateTime
     */
    public function getAssignDate(): \DateTime
    {
        return $this->assignDate;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }
}
