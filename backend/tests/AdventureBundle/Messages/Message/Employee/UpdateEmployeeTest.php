<?php

namespace Tests\Divante\Bundle\AdventureBundle\Messages\Message\Employee;

use Divante\Bundle\AdventureBundle\Mappers\EmployeeRequestMapper;
use Divante\Bundle\AdventureBundle\Message\Employee\UpdateEmployee;
use PHPUnit\Framework\TestCase;
use Tests\AdventureTestUtils;

class UpdateEmployeeTest extends TestCase
{
    private object $message;
    protected function setUp(): void
    {
        $this->message = (new EmployeeRequestMapper())
            ->mapToMessage(
                AdventureTestUtils::createRequest(
                    [
                        'id' => 1,
                        'nip' => '3816300274',
                        'firmName' => 'Testowa firma',
                        'firmAddress' => 'Testowy adres firmy'
                    ]
                )
            );
        parent::setUp();
    }

    public function testExistAttributes(): void
    {
        $this->assertClassHasAttribute('nip', UpdateEmployee::class);
        $this->assertClassHasAttribute('firmName', UpdateEmployee::class);
        $this->assertClassHasAttribute('firmAddress', UpdateEmployee::class);
    }
    public function testGetFirmAddress()
    {
        $this->assertEquals('3816300274', $this->message->getNip());
    }

    public function testGetNip()
    {
        $this->assertEquals('Testowa firma', $this->message->getFirmName());
    }

    public function testGetFirmName()
    {
        $this->assertEquals('Testowy adres firmy', $this->message->getFirmAddress());
    }
}
