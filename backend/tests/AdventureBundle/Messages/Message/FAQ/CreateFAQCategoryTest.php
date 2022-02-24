<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Tests\AdventureBundle\Messages\Message\FAQ;

use Tests\FoundationTestCase;
use Divante\Bundle\AdventureBundle\Message\FAQ\CreateFAQCategory;

class CreateFAQCategoryTest extends FoundationTestCase
{
    public function testNamePlReturnedCorrectly() : void
    {
        $employees = [rand(0, 10000), rand(0, 10000)];
        $namePl = "NamePl".rand(0, 10000);
        $nameEn = "NameEn".rand(0, 10000);
        $message = new CreateFAQCategory($employees, $namePl, $nameEn);
        $this->assertEquals($namePl, $message->getNamePl());
    }

    public function testNameEnReturnedCorrectly() : void
    {
        $employees = [rand(0, 10000), rand(0, 10000)];
        $namePl = "NamePl".rand(0, 10000);
        $nameEn = "NameEn".rand(0, 10000);
        $message = new CreateFAQCategory($employees, $namePl, $nameEn);
        $this->assertEquals($nameEn, $message->getNameEn());
    }
}
