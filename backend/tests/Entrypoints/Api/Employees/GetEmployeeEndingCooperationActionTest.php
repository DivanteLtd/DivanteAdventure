<?php

namespace Tests\Entrypoints\Api\Employees;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\EmployeeEndCooperation;
use Divante\Bundle\AdventureBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class GetEmployeeEndingCooperationActionTest extends AbstractEmployeesTest
{
    private ?User $user = null;
    private ?Employee $employee = null;
    private ?EmployeeEndCooperation $endCooperation = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->generateFosUser(['ROLE_SUPER_ADMIN']);
        $this->employee = $this->generateEmployee($this->user, null, rand(1, 3));
        $this->endCooperation = new EmployeeEndCooperation();
        $this->endCooperation
            ->setEmployee($this->employee)
            ->setName($this->employee->getName())
            ->setLastName($this->employee->getLastName())
            ->setChecklist((bool)rand(0,1))
            ->setExitInterview((bool)rand(0,1))
            ->setNextCompany("RandomCompanyName".rand(0, 10000))
            ->setCreatedAt()
            ->setUpdatedAt();
        $this->em->persist($this->endCooperation);
        $this->em->flush();
    }

    protected function tearDown() : void
    {
        $this->em->remove($this->endCooperation);
        $this->em->remove($this->employee);
        $this->em->remove($this->user);
        $this->em->flush();
        parent::tearDown();
    }

    public function testEntrypoint() : void
    {
        $url = '/api/employees/endedWork';
        $response = $this->request("GET", $url, $this->user);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        /** @var array<int,array<string,mixed>> $json */
        $json = json_decode($response->getContent(), true);
        $this->assertGreaterThanOrEqual(1, count($json));
        /** @var array<int,array<string,mixed>> $filtered */
        $filtered = array_filter($json, fn(array $entry) => $entry['id'] === $this->endCooperation->getId());
        $this->assertCount(1, $filtered);
        /** @var array<string,mixed> $entry */
        $entry = array_pop($filtered);
        $this->assertArrayHasKey('name', $entry);
        $this->assertEquals($this->employee->getName(), $entry['name']);
    }
}
