<?php

namespace Divante\Bundle\AdventureBundle\Message\Employee;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class CreateEmployeeEndingCooperation
{
    use ObjectTrait;

    private string $email;
    private ?string $nextCompany;
    private string $whoEndedCooperation;
    private bool $exitInterview;
    private bool $checklist;
    private ?string $comment;
    private string $dismissDate;

    public function __construct(
        string $email,
        ?string $nextCompany,
        string $whoEndedCooperation,
        bool $exitInterview,
        bool $checklist,
        ?string $comment,
        string $dismissDate
    ) {
        $this->email = $email;
        $this->nextCompany = $nextCompany;
        $this->whoEndedCooperation = $whoEndedCooperation;
        $this->exitInterview = $exitInterview;
        $this->checklist = $checklist;
        $this->comment = $comment;
        $this->dismissDate = $dismissDate;
    }

    public function getEmail(): string
    {
        return $this->email;
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

    public function getDismissDate(): string
    {
        return $this->dismissDate;
    }
}
