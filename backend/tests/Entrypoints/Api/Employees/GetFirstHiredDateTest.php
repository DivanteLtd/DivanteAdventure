<?php

namespace Tests\Entrypoints\Api\Employees;

use DateTime;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class GetFirstHiredDateTest extends AbstractEmployeesTest
{
    private ?User $user = null;
    private ?Employee $employee = null;
    private ?DateTime $hiredAt = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->hiredAt = (new DateTime())->setDate(1950, 1, 1);
        $this->user = $this->generateFosUser(['ROLE_USER']);
        $this->employee = $this->generateEmployee($this->user);
        $this->employee->setHiredAt($this->hiredAt);
        $this->em->flush();
    }

    protected function tearDown() : void
    {
        $this->em->remove($this->employee);
        $this->em->remove($this->user);
        $this->em->flush();
        parent::tearDown();
    }

    public function testEntrypoint() : void
    {
        $url = '/api/employees/firstHiredDate';
        $response = $this->request("GET", $url, $this->user);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        /** @var array<int,array<string,string>> $json */
        $json = json_decode($response->getContent(), true);
        $this->assertCount(1, $json);
        $entry = array_pop($json);
        $this->assertArrayHasKey('hiredAt', $entry);
        $this->assertEquals($this->hiredAt->format('Y-m-d'), $entry['hiredAt']);
    }
}