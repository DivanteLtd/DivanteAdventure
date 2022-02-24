<?php

namespace Tests\AdventureBundle\Messages\Message\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Message\Checklist\UpdateQuestionStatus;
use Tests\FoundationTestCase;

class UpdateQuestionStatusTest extends FoundationTestCase
{
    public function testChecklistIdPassed() : void
    {
        $checklistId = rand(0, 10000);
        $questionId = rand(0, 10000);
        $status = rand(0, 10000);
        $employee = new Employee();

        $message = new UpdateQuestionStatus($checklistId, $questionId, $status, $employee);
        $this->assertEquals($checklistId, $message->getChecklistId());
    }

    public function testQuestionIdPassed() : void
    {
        $checklistId = rand(0, 10000);
        $questionId = rand(0, 10000);
        $status = rand(0, 10000);
        $employee = new Employee();

        $message = new UpdateQuestionStatus($checklistId, $questionId, $status, $employee);
        $this->assertEquals($questionId, $message->getQuestionId());
    }

    public function testStatusPassed() : void
    {
        $checklistId = rand(0, 10000);
        $questionId = rand(0, 10000);
        $status = rand(0, 10000);
        $employee = new Employee();

        $message = new UpdateQuestionStatus($checklistId, $questionId, $status, $employee);
        $this->assertEquals($status, $message->getStatus());
    }

    public function testEmployeePassed() : void
    {
        $checklistId = rand(0, 10000);
        $questionId = rand(0, 10000);
        $status = rand(0, 10000);
        $employee = new Employee();

        $message = new UpdateQuestionStatus($checklistId, $questionId, $status, $employee);
        $this->assertSame($employee, $message->getEmployee());
    }
}
