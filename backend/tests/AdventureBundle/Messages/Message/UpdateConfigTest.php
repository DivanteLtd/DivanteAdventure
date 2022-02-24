<?php

namespace Tests\AdventureBundle\Messages\Message;

use Divante\Bundle\AdventureBundle\Message\UpdateConfig;
use PHPUnit\Framework\TestCase;

class UpdateConfigTest extends TestCase
{
    public function testEmployeeId(): void
    {
        $entries = [[ 'foo' => 'bar'.rand(0, 10000) ]];
        $employeeId = rand(0, 10000);
        $message = new UpdateConfig($entries, $employeeId);
        $this->assertSame($employeeId, $message->getEmployeeId());
    }

    public function testEntries(): void
    {
        $entries = [[ 'foo' => 'bar'.rand(0, 10000) ]];
        $employeeId = rand(0, 10000);
        $message = new UpdateConfig($entries, $employeeId);
        $this->assertSame($entries, $message->getEntries());
    }
}
