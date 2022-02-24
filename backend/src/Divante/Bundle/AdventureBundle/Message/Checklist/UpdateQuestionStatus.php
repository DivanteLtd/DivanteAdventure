<?php

namespace Divante\Bundle\AdventureBundle\Message\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class UpdateQuestionStatus
{
    use ObjectTrait;

    private int $checklistId;
    private int $questionId;
    private int $status;
    private Employee $employee;

    public function __construct(int $checklistId, int $questionId, int $status, Employee $employee)
    {
        $this->checklistId = $checklistId;
        $this->questionId = $questionId;
        $this->status = $status;
        $this->employee = $employee;
    }

    public function getChecklistId(): int
    {
        return $this->checklistId;
    }

    public function getQuestionId(): int
    {
        return $this->questionId;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getEmployee(): Employee
    {
        return $this->employee;
    }
}
