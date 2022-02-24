<?php

namespace Tests\AdventureBundle\Messages\Message\Checklist;

use Divante\Bundle\AdventureBundle\Message\FAQ\DeleteFAQCategory;
use Tests\FoundationTestCase;

class DeleteFAQCategoryTest extends FoundationTestCase
{
    public function testCorrectIdStored() : void
    {
        $id = rand(0, 10000);
        $message = new DeleteFAQCategory($id);
        $this->assertEquals($id, $message->getId());
    }
}
