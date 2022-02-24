<?php

namespace Tests\Entrypoints\Api\Employees;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class UpdateActionTest extends AbstractEmployeesTest
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
        $newLastName = "RandomEditedLastName".rand(0, 10000);
        $url = '/api/employees/'.$this->employee->getId();
        $response = $this->request("PATCH", $url, $this->user, [
            'lastName' => $newLastName,
        ]);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        /** @var array $json */

        $json = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('name', $json);
        $this->assertEquals($this->employee->getName(), $json['name']);
        $this->assertArrayHasKey('lastName', $json);
        $this->assertEquals($newLastName, $json['lastName']);
    }
}
