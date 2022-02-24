<?php

namespace Tests\Entrypoints\Api\PotentialEmployee;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\PotentialEmployee;
use Divante\Bundle\AdventureBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class GetEmployeesTest extends AbstractPotentialEmployeeTest
{
    private ?User $user = null;
    private ?Employee $employee = null;
    private ?PotentialEmployee $potentialEmployee = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->generateFosUser(['ROLE_HR']);
        $this->employee = $this->generateEmployee($this->user);
        $this->potentialEmployee = $this->generatePotentialEmployee($this->employee);
        $this->em->flush();
    }

    protected function tearDown(): void
    {
        $this->em->remove($this->potentialEmployee);
        $this->em->remove($this->employee);
        $this->em->remove($this->user);
        $this->em->flush();
        parent::tearDown();
    }

    public function testEntrypoint() : void
    {
        $url = '/api/potential_employee';
        $response = $this->request('GET', $url, $this->user);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        /** @var array<int,array<string,mixed>> $json */
        $json = json_decode($response->getContent(), true);
        $this->assertGreaterThanOrEqual(1, count($json));

        /** @var array<int,array<string,mixed>> $json */
        $filteredEntries = array_filter($json, fn(array $entry) => $entry['id'] === $this->potentialEmployee->getId());
        $this->assertCount(1, $filteredEntries);

        /** @var array<string,mixed> $entry */
        $entry = array_pop($filteredEntries);
        $this->assertEquals($this->potentialEmployee->getName(), $entry['name']);
    }
}