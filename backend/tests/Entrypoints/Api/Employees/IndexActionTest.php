<?php

namespace Tests\Entrypoints\Api\Employees;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class IndexActionTest extends AbstractEmployeesTest
{
    private ?User $user1 = null;
    private ?User $user2 = null;
    private ?User $user3 = null;
    private ?Employee $employee1 = null;
    private ?Employee $employee2 = null;
    private ?Employee $employee3 = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user1 = $this->generateFosUser(['ROLE_MANAGER']);
        $this->user2 = $this->generateFosUser();
        $this->user3 = $this->generateFosUser();
        $this->employee1 = $this->generateEmployee($this->user1);
        $this->employee2 = $this->generateEmployee($this->user2);
        $this->employee3 = $this->generateEmployee($this->user3);
        $this->em->flush();
    }

    protected function tearDown() : void
    {
        $this->em->remove($this->employee1);
        $this->em->remove($this->employee2);
        $this->em->remove($this->employee3);
        $this->em->remove($this->user1);
        $this->em->remove($this->user2);
        $this->em->remove($this->user3);
        $this->em->flush();
        parent::tearDown();
    }

    public function testEntrypoint() : void
    {
        $response = $this->request("GET", "/api/employees", $this->user1);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        /** @var array $json */
        $json = json_decode($response->getContent(), true);
        $this->assertGreaterThanOrEqual(3, count($json));

        $requiredIds = [ $this->employee1->getId(), $this->employee2->getId(), $this->employee3->getId() ];
        $values = array_filter($json, fn(array $data) : bool => in_array($data['id'], $requiredIds));
        $this->assertCount(3, $values);

        $employee = $this->employee1;
        $firstValueContainer = array_filter($values, fn(array $data) : bool => $data['id'] === $employee->getId());
        $this->assertCount(1, $firstValueContainer);
        /** @var array<string,mixed> $firstValue */
        $firstValue = array_pop($firstValueContainer);
        $this->assertArrayHasKey('name', $firstValue);
        $this->assertEquals($employee->getName(), $firstValue['name']);
        $this->assertArrayHasKey('lastName', $firstValue);
        $this->assertEquals($employee->getLastName(), $firstValue['lastName']);
    }
}
