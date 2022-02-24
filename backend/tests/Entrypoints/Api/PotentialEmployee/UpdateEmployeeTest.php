<?php

namespace Tests\Entrypoints\Api\PotentialEmployee;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\PotentialEmployee;
use Divante\Bundle\AdventureBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class UpdateEmployeeTest extends AbstractPotentialEmployeeTest
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
        $url = '/api/potential_employee/'.$this->potentialEmployee->getId();
        $data = [
            'name' => "RandomNewName".rand(0, 10000),
            'status' => PotentialEmployee::STATUS_POTENTIAL_EMPLOYEE,
        ];
        $this->assertNotEquals($data['name'], $this->potentialEmployee->getName());

        $response = $this->request('PATCH', $url, $this->user, $data);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        /** @var array<int,array<string,mixed>> $json */
        $json = json_decode($response->getContent(), true);
        $this->assertEmpty($json);

        $this->em->refresh($this->potentialEmployee);
        $this->assertEquals($data['name'], $this->potentialEmployee->getName());
    }
}