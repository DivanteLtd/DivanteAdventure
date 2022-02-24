<?php

namespace Divante\Bundle\AdventureBundle\Message\Employee;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class UpdateEmployeeEndingCooperation
{
    use ObjectTrait;
    /** @var integer */
    private $id;
    /** @var integer */
    private $employeeId;
    /** @var string|null */
    private $nextCompany;
    /** @var string */
    private $whoEndedCooperation;
    /** @var boolean */
    private $exitInterview;
    /** @var boolean */
    private $checklist;
    /** @var string|null */
    private $comment;
    /** @var string */
    private $dismissDate;

    /**
     *
     * @param int $id
     * @param int $employeeId
     * @param string|null $nextCompany
     * @param string $whoEndedCooperation
     * @param bool $exitInterview
     * @param bool $checklist
     * @param string|null $comment
     * @param string $dismissDate
     */
    public function __construct(
        int $id,
        int $employeeId,
        ?string $nextCompany,
        string $whoEndedCooperation,
        bool $exitInterview,
        bool $checklist,
        ?string $comment,
        string $dismissDate
    ) {
        $this->id = $id;
        $this->employeeId = $employeeId;
        $this->nextCompany = $nextCompany;
        $this->whoEndedCooperation = $whoEndedCooperation;
        $this->exitInterview = $exitInterview;
        $this->checklist = $checklist;
        $this->comment = $comment;
        $this->dismissDate = $dismissDate;
    }

    public function getDismissDate(): \DateTime
    {
        return new \DateTime($this->dismissDate);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmployeeId(): int
    {
        return $this->employeeId;
    }

    public function getNextCompany(): ?string
    {
        return $this->nextCompany;
    }

    public function getWhoEndedCooperation(): string
    {
        return $this->whoEndedCooperation;
    }

    public function isExitInterview(): bool
    {
        return $this->exitInterview;
    }

    public function isChecklist(): bool
    {
        return $this->checklist;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }
}
