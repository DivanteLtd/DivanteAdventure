<?php


namespace Divante\Bundle\AdventureBundle\Entity;

use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Divante\Bundle\AdventureBundle\Entity\Data\Timestampable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Contract
 *
 * @ORM\Table(name="contract")
 * @ORM\Entity
 */
class Contract
{
    use Timestampable, Id;

    /**
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\ContractType", inversedBy="contracts")
     */
    private ?ContractType $type;
    /**
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Employee", inversedBy="employees")
     */
    private ?Employee $employee;
    /** @ORM\Column(name="start_date", type="datetime", nullable=true) */
    private ?\DateTime $startDate;
    /** @ORM\Column(name="end_date", type="datetime", nullable=true) */
    private ?\DateTime $endDate;
    /** @ORM\Column(name="assign_date", type="datetime", nullable=true) */
    private ?\DateTime $assignDate;
    /** @ORM\Column(name="notice_period", type="integer", nullable=true) */
    private ?int $noticePeriod;
    /** @ORM\Column(name="active", type="boolean") */
    private bool $active = true;

    public function getType(): ?ContractType
    {
        return $this->type;
    }


    public function setType(?ContractType $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime|null $startDate
     */
    public function setStartDate(?\DateTime $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTime $endDate): self
    {
        $this->endDate = $endDate;
        return $this;
    }

    public function getAssignDate(): ?\DateTime
    {
        return $this->assignDate;
    }

    public function setAssignDate(?\DateTime $assignDate): self
    {
        $this->assignDate = $assignDate;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;
        return $this;
    }

    public function getNoticePeriod(): ?int
    {
        return $this->noticePeriod;
    }

    public function setNoticePeriod(?int $noticePeriod): self
    {
        $this->noticePeriod = $noticePeriod;
        return $this;
    }

    public function setEmployee(Employee $employee): self
    {
        $this->employee = $employee;
        return $this;
    }

    public function getEmployee() :Employee
    {
        return $this->employee;
    }
}
