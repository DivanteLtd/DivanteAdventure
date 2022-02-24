<?php

namespace Tests\AdventureBundle\Employee;

use Divante\Bundle\AdventureBundle\Mappers\EmployeeRequestMapper;
use Divante\Bundle\AdventureBundle\Message\Employee\UpdateEmployee;
use PHPUnit\Framework\TestCase;
use Tests\AdventureTestUtils;

class EmployeeRequestMapperTest extends TestCase
{
    private object $message;

    protected function setUp(): void
    {
        $this->message = (new EmployeeRequestMapper())->mapToMessage(AdventureTestUtils::createRequest(['id' => 1]));
        parent::setUp();
    }


    public function testMapToMessage(): void
    {
        $this->assertInstanceOf(UpdateEmployee::class, $this->message);
    }

}
