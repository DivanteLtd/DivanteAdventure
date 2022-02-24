<?php

namespace Tests\AdventureBundle\Messages\Message\Checklist;

use Divante\Bundle\AdventureBundle\Message\Checklist\DeleteChecklistTemplate;
use Tests\FoundationTestCase;

class DeleteChecklistTemplateTest extends FoundationTestCase
{
    public function testCorrectIdStored() : void
    {
        $id = rand(0, 10000);
        $message = new DeleteChecklistTemplate($id);
        $this->assertEquals($id, $message->getChecklistId());
    }
}
