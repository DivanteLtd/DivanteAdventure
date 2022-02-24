<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Tests\AdventureBundle\Messages\Message\FAQ;

use Divante\Bundle\AdventureBundle\Message\FAQ\UpdateFAQCategory;
use Tests\FoundationTestCase;

class UpdateFAQCategoryTest extends FoundationTestCase
{

    public function testIdReturnedCorrectly() : void
    {
        $id = rand(0, 10000);
        $employees = [rand(0, 10000), rand(0, 10000)];
        $namePl = "RandomNamePl".rand(0, 10000);
        $nameEn = "RandomNameEn".rand(0, 10000);
        $message = new UpdateFAQCategory($id, $employees, $namePl, $nameEn);

        $this->assertEquals($id, $message->getId());
    }

    public function testNamePlReturnedCorrectly() : void
    {
        $id = rand(0, 10000);
        $employees = [rand(0, 10000), rand(0, 10000)];
        $namePl = "RandomNamePl".rand(0, 10000);
        $nameEn = "RandomNameEn".rand(0, 10000);
        $message = new UpdateFAQCategory($id, $employees, $namePl, $nameEn);
        $this->assertEquals($namePl, $message->getNamePl());
    }

    public function testEmployeesReturnedCorrectly() : void
    {
        $id = rand(0, 10000);
        $employees = [rand(0, 10000), rand(0, 10000)];
        $namePl = "RandomNamePl".rand(0, 10000);
        $nameEn = "RandomNameEn".rand(0, 10000);
        $message = new UpdateFAQCategory($id, $employees, $namePl, $nameEn);
        $this->assertEquals($employees, $message->getEmployees());
    }

    public function testNameEnReturnedCorrectly() : void
    {
        $id = rand(0, 10000);
        $employees = [rand(0, 10000), rand(0, 10000)];
        $namePl = "RandomNamePl".rand(0, 10000);
        $nameEn = "RandomNameEn".rand(0, 10000);
        $message = new UpdateFAQCategory($id, $employees, $namePl, $nameEn);
        $this->assertEquals($nameEn, $message->getNameEn());
    }
}
