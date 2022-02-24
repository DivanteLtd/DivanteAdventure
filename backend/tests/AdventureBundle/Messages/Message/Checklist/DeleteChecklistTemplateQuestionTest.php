<?php

namespace Tests\AdventureBundle\Messages\Message\Checklist;

use Divante\Bundle\AdventureBundle\Message\Checklist\DeleteChecklistTemplateQuestion;
use Tests\FoundationTestCase;

class DeleteChecklistTemplateQuestionTest extends FoundationTestCase
{
    public function testTemplateIdPassed() : void
    {
        $templateId = rand(0, 10000);
        $questionId = rand(0, 10000);
        $message = new DeleteChecklistTemplateQuestion($templateId, $questionId);
        $this->assertEquals($templateId, $message->getTemplateId());
    }

    public function testQuestionIdPassed() : void
    {
        $templateId = rand(0, 10000);
        $questionId = rand(0, 10000);
        $message = new DeleteChecklistTemplateQuestion($templateId, $questionId);
        $this->assertEquals($questionId, $message->getQuestionId());
    }
}
