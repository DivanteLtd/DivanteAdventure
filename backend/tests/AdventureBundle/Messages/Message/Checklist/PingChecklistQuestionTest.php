<?php

namespace Tests\AdventureBundle\Messages\Message\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Message\Checklist\PingChecklistQuestion;
use Tests\FoundationTestCase;

class PingChecklistQuestionTest extends FoundationTestCase
{
    public function testCorrectIdPassed() : void
    {
        $id = rand(0, 10000);
        $message = new PingChecklistQuestion($id, new Employee());
        $this->assertEquals($id, $message->getQuestionId());
    }

    public function testCorrectEmployeePassed() : void
    {
        $employee = new Employee();
        $message = new PingChecklistQuestion(rand(0, 10000), $employee);
        $this->assertSame($employee, $message->getUser());
    }
}
