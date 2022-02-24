<?php

namespace Divante\Bundle\AdventureBundle\Message\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class PingChecklistQuestion
{
    use ObjectTrait;

    private int $questionId;
    private Employee $user;

    public function __construct(int $questionId, Employee $user)
    {
        $this->questionId = $questionId;
        $this->user = $user;
    }

    public function getQuestionId(): int
    {
        return $this->questionId;
    }

    public function getUser(): Employee
    {
        return $this->user;
    }
}
