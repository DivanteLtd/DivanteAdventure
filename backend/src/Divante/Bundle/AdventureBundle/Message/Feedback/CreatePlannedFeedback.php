<?php

namespace Divante\Bundle\AdventureBundle\Message\Feedback;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CreatePlannedFeedback
{
    use ObjectTrait;

    private int $employeeId;
    private int $leaderId;
    private string $date;

    public function __construct(int $employeeId, int $leaderId, string $date)
    {
        $this->employeeId = $employeeId;
        $this->leaderId = $leaderId;
        $this->date = $date;
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) !== 1) {
            throw new BadRequestHttpException("Date must be in YYYY-MM-DD format");
        }
    }

    public function getEmployeeId(): int
    {
        return $this->employeeId;
    }

    public function getLeaderId(): int
    {
        return $this->leaderId;
    }

    public function getDate(): string
    {
        return $this->date;
    }
}
