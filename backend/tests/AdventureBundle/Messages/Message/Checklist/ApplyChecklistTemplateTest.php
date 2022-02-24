<?php

namespace Tests\AdventureBundle\Messages\Message\Checklist;

use Divante\Bundle\AdventureBundle\Message\Checklist\ApplyChecklistTemplate;
use Tests\FoundationTestCase;

class ApplyChecklistTemplateTest extends FoundationTestCase
{
    public function testTemplateIdPassed() : void
    {
        $templateId = rand(0, 10000);
        $hidden = (bool)random_int(0, 1);
        $message = new ApplyChecklistTemplate(
            $templateId,
            [rand(0, 10000)],
            rand(0, 10000),
            $hidden,
            rand(0, 10000),
            '2020-11-18'
        );
        $this->assertEquals($templateId, $message->getTemplateId());
    }

    public function testOwnerIdNullPassed() : void
    {
        $hidden = (bool)random_int(0, 1);
        $message = new ApplyChecklistTemplate(
            rand(1, 10000),
            [],
            rand(10001, 20000),
            $hidden,
            rand(1, 10000),
            '2020-11-18'
        );
        $this->assertEmpty($message->getOwnerIds());
    }

    public function testOwnerIdValuePassed() : void
    {
        $ownerId = rand(0, 10000);
        $hidden = (bool)random_int(0, 1);
        $message = new ApplyChecklistTemplate(
            rand(0, 10000),
            [$ownerId],
            rand(0, 10000),
            $hidden,
            rand(0, 10000),
            '2020-11-18'
        );
        $this->assertEquals([$ownerId], $message->getOwnerIds());
    }

    public function testSubjectIdPassed() : void
    {
        $subjectId = rand(0, 10000);
        $hidden = (bool)random_int(0, 1);
        $message = new ApplyChecklistTemplate(
            rand(0, 10000),
            [rand(0, 10000)],
            $subjectId,
            $hidden,
            rand(0, 10000),
            '2020-11-18'
        );
        $this->assertEquals($subjectId, $message->getSubjectId());
    }

    public function testUserEmployeeIdPassed() : void
    {
        $userEmployeeId = rand(0, 10000);
        $hidden = (bool)random_int(0, 1);
        $message = new ApplyChecklistTemplate(
            rand(0, 10000),
            [rand(0, 10000)],
            rand(0, 10000),
            $hidden,
            $userEmployeeId,
            '2020-11-18'
        );
        $this->assertEquals($userEmployeeId, $message->getUserEmployeeId());
    }
}
