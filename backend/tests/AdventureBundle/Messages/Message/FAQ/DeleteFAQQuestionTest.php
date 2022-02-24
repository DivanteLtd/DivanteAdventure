<?php

namespace Tests\AdventureBundle\Messages\Message\Checklist;

use Divante\Bundle\AdventureBundle\Message\FAQ\DeleteFAQQuestion;
use Tests\FoundationTestCase;

class DeleteFAQQuestionTest extends FoundationTestCase
{
    public function testCorrectIdStored() : void
    {
        $id = rand(0, 10000);
        $employeeId = rand(0, 10000);
        $message = new DeleteFAQQuestion($id, $employeeId);
        $this->assertEquals($id, $message->getId());
    }

    public function testCorrectEmployeeIdStored() : void
    {
        $id = rand(0, 10000);
        $employeeId = rand(0, 10000);
        $message = new DeleteFAQQuestion($id, $employeeId);
        $this->assertEquals($employeeId, $message->getEmployeeId());
    }
}
