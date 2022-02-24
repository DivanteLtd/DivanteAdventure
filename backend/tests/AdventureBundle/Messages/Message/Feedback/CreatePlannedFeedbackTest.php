<?php

namespace Tests\AdventureBundle\Messages\Message\Feedback;

use DateTime;
use Divante\Bundle\AdventureBundle\Message\Feedback\CreatePlannedFeedback;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CreatePlannedFeedbackTest extends TestCase
{
    public function testEmployeeIdPassed(): void
    {
        $employeeId = rand(0, 10000);
        $leaderId = rand(0, 10000);
        $date = (new DateTime())->setDate(rand(2000, 2100), rand(1, 12), rand(1, 25))->format('Y-m-d');
        $message = new CreatePlannedFeedback($employeeId, $leaderId, $date);
        $this->assertSame($employeeId, $message->getEmployeeId());
    }

    public function testLeaderIdPassed(): void
    {
        $employeeId = rand(0, 10000);
        $leaderId = rand(0, 10000);
        $date = (new DateTime())->setDate(rand(2000, 2100), rand(1, 12), rand(1, 25))->format('Y-m-d');
        $message = new CreatePlannedFeedback($employeeId, $leaderId, $date);
        $this->assertSame($leaderId, $message->getLeaderId());
    }

    public function testDatePassed(): void
    {
        $employeeId = rand(0, 10000);
        $leaderId = rand(0, 10000);
        $date = (new DateTime())->setDate(rand(2000, 2100), rand(1, 12), rand(1, 25))->format('Y-m-d');
        $message = new CreatePlannedFeedback($employeeId, $leaderId, $date);
        $this->assertSame($date, $message->getDate());
    }

    public function testExceptionOnInvalidDate(): void
    {
        $employeeId = rand(0, 10000);
        $leaderId = rand(0, 10000);
        $date = "invalid date";
        $this->expectException(BadRequestHttpException::class);
        new CreatePlannedFeedback($employeeId, $leaderId, $date);
    }
}
