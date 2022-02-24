<?php

namespace Tests\AdventureBundle\Messages\Message\Checklist;

use Divante\Bundle\AdventureBundle\Message\Checklist\CreateChecklistTemplate;
use Tests\FoundationTestCase;

class CreateChecklistTemplateTest extends FoundationTestCase
{
    public function testTypeReturnedCorrectly() : void
    {
        $type = rand(0, 10000);
        $namePl = "NamePl".rand(0, 10000);
        $nameEn = "NameEn".rand(0, 10000);
        $message = new CreateChecklistTemplate($type, $namePl, $nameEn);
        $this->assertEquals($type, $message->getType());
    }

    public function testNamePlReturnedCorrectly() : void
    {
        $type = rand(0, 10000);
        $namePl = "NamePl".rand(0, 10000);
        $nameEn = "NameEn".rand(0, 10000);
        $message = new CreateChecklistTemplate($type, $namePl, $nameEn);
        $this->assertEquals($namePl, $message->getNamePl());
    }

    public function testNameEnReturnedCorrectly() : void
    {
        $type = rand(0, 10000);
        $namePl = "NamePl".rand(0, 10000);
        $nameEn = "NameEn".rand(0, 10000);
        $message = new CreateChecklistTemplate($type, $namePl, $nameEn);
        $this->assertEquals($nameEn, $message->getNameEn());
    }
}
