<?php

namespace Tests\AdventureBundle\Messages\Message\Checklist;

use Divante\Bundle\AdventureBundle\Message\Checklist\EditChecklistTemplate;
use Tests\FoundationTestCase;

class EditChecklistTemplateTest extends FoundationTestCase
{
    public function testNoNames() : void
    {
        $id = rand(0, 10000);
        $message = new EditChecklistTemplate($id, null, null);
        $this->assertNull($message->getNamePl());
        $this->assertNull($message->getNameEn());
        $this->assertEquals($id, $message->getId());
    }

    public function testNamePlPassed() : void
    {
        $id = rand(0, 10000);
        $namePl = "RandomNamePl" . rand(0, 10000);
        $message = new EditChecklistTemplate($id, $namePl, null);
        $this->assertEquals($namePl, $message->getNamePl());
        $this->assertNull($message->getNameEn());
        $this->assertEquals($id, $message->getId());
    }

    public function testNameEnPassed() : void
    {
        $id = rand(0, 10000);
        $nameEn = "RandomNameEn" . rand(0, 10000);
        $message = new EditChecklistTemplate($id, null, $nameEn);
        $this->assertNull($message->getNamePl());
        $this->assertEquals($nameEn, $message->getNameEn());
        $this->assertEquals($id, $message->getId());
    }

    public function testBothNamesPassed() : void
    {
        $id = rand(0, 10000);
        $namePl = "RandomNamePl" . rand(0, 10000);
        $nameEn = "RandomNameEn" . rand(0, 10000);
        $message = new EditChecklistTemplate($id, $namePl, $nameEn);
        $this->assertEquals($namePl, $message->getNamePl());
        $this->assertEquals($nameEn, $message->getNameEn());
        $this->assertEquals($id, $message->getId());
    }
}
