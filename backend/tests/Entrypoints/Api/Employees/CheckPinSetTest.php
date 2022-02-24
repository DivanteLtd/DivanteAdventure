<?php

namespace Tests\Entrypoints\Api\Employees;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class CheckPinSetTest extends AbstractEmployeesTest
{
    private ?User $user = null;
    private ?Employee $employee = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->generateFosUser(['ROLE_MANAGER']);
        $this->employee = $this->generateEmployee($this->user);
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
        $url = '/api/employees/isPinSet';
        $response = $this->request("GET", $url, $this->user);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        /** @var array<string,bool> $json */
        $json = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('hasSetPin', $json);
        $this->assertTrue($json['hasSetPin']);
    }
}
