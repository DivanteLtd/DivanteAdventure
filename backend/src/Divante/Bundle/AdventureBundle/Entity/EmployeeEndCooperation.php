<?php

namespace Divante\Bundle\AdventureBundle\Entity;

use DateTime;
use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Divante\Bundle\AdventureBundle\Entity\Data\Timestampable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Employee
 *
 * @ORM\Table(name="employee_end_cooperation")
 * @ORM\Entity
 */
class EmployeeEndCooperation
{
    use Timestampable, Id;

    public const ENDED_BY_COMPANY = 'Company';
    public const ENDED_BY_EMPLOYEE = 'Employee';


    /**
     * @ORM\OneToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Employee", inversedBy="endingCooperation")
     * @ORM\JoinColumn(name="employee_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private ?Employee $employee = null;

    /** @ORM\Column(name="name", type="string", length=254, nullable=true) */
    private ?string $name = null;
    /** @ORM\Column(name="lastName", type="string", length=254, nullable=true) */
    private ?string $lastName = null;
    /** @ORM\Column(name="position", type="string", length=254, nullable=true) */
    private ?string $position = null;
    /** @ORM\Column(name="dismiss_date", type="date", nullable=true) */
    private ?DateTime $dismissDate = null;
    /** @ORM\Column(name="next_company", type="string", length=254, nullable=true) */
    private ?string $nextCompany = null;
    /** @ORM\Column(name="who_ended_cooperation", type="string", length=150, nullable=true) */
    private ?string $whoEndedCooperation = null;
    /** @ORM\Column(name="exit_interview", type="boolean", nullable=true) */
    private ?bool $exitInterview = null;
    /** @ORM\Column(name="checklist", type="boolean", nullable=true) */
    private ?bool $checklist = null;
    /** @ORM\Column(name="comment", type="string", length=500, nullable=true) */
    private ?string $comment = null;

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee): self
    {
        $this->employee = $employee;
        return $this;
    }

    public function getNextCompany(): ?string
    {
        return $this->nextCompany;
    }

    public function setNextCompany(?string $nextCompany): self
    {
        $this->nextCompany = $nextCompany;
        return $this;
    }

    public function getWhoEndedCooperation(): ?string
    {
        return $this->whoEndedCooperation;
    }

    public function setWhoEndedCooperation(?string $whoEndedCooperation): self
    {
        $this->whoEndedCooperation = $whoEndedCooperation;
        return $this;
    }

    public function isExitInterview(): ?bool
    {
        return $this->exitInterview;
    }

    public function setExitInterview(?bool $exitInterview): self
    {
        $this->exitInterview = $exitInterview;
        return $this;
    }

    public function isChecklist(): ?bool
    {
        return $this->checklist;
    }

    public function setChecklist(?bool $checklist): self
    {
        $this->checklist = $checklist;
        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(?string $position): self
    {
        $this->position = $position;
        return $this;
    }

    public function getDismissDate() : ?DateTime
    {
        return $this->dismissDate;
    }

    public function setDismissDate(?DateTime $dateTime) : self
    {
        $this->dismissDate = $dateTime;
        return $this;
    }
}
