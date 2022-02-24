<?php

namespace Divante\Bundle\AdventureBundle\Query\Employee;

class EmployeeEndCooperationView
{
    private int $id;
    private int $employeeId;
    private string $name;
    private string $lastName;
    private ?string $contract;
    private ?string $nextCompany;
    private ?string $whoEndedCooperation;
    private bool $exitInterview;
    private bool $checklist;
    private ?string $comment;
    private ?string $dismissDate;
    private string $email;

    public function __construct(
        int $id,
        int $employeeId,
        string $name,
        string $lastName,
        ?string $contract,
        ?string $nextCompany,
        ?string $whoEndedCooperation,
        bool $exitInterview,
        bool $checklist,
        ?string $comment,
        ?string $dismissDate,
        string $email
    ) {
        $this->id = $id;
        $this->employeeId = $employeeId;
        $this->name = $name;
        $this->lastName = $lastName;
        $this->contract = $contract;
        $this->nextCompany = $nextCompany;
        $this->whoEndedCooperation = $whoEndedCooperation;
        $this->exitInterview = $exitInterview;
        $this->checklist = $checklist;
        $this->comment = $comment;
        $this->dismissDate = $dismissDate;
        $this->email = $email;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getNextCompany(): ?string
    {
        return $this->nextCompany;
    }

    public function getWhoEndedCooperation(): ?string
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

    public function getDismissDate(): ?string
    {
        return $this->dismissDate;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getEmployeeId(): int
    {
        return $this->employeeId;
    }

    public function getContract(): ?string
    {
        return $this->contract;
    }
}
