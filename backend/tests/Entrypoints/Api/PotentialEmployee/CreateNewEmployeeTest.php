<?php

namespace Tests\Entrypoints\Api\PotentialEmployee;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\PotentialEmployee;
use Divante\Bundle\AdventureBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class CreateNewEmployeeTest extends AbstractPotentialEmployeeTest
{
    private ?User $user = null;
    private ?Employee $employee = null;
    private ?PotentialEmployee $potentialEmployee = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->generateFosUser(['ROLE_HR']);
        $this->employee = $this->generateEmployee($this->user);
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
        $data = [
            'name' => "RandomName".rand(0, 10000),
            'lastName' => "RandomLastName".rand(0, 10000),
            'email' => "randomEmail".rand(0, 10000)."@example.com",
            'tribeId' => null,
            'positionId' => null,
            'hireDate' => null,
            'contractType' => "randomContract".rand(0, 10000),
            'gender' => rand(0, 10000),
            'dateOfBirth' => "1999-09-09",
            'privatePhone' => rand(0, 10000),
            'privateEmail' => "randomPrivateEmail".rand(0, 10000)."@example.com",
            'workMode' => 1,
            'city' => "randomCity".rand(0, 10000),
            'postalCode' => "randomPostalCode".rand(0, 10000),
            'street' => "randomStreet".rand(0, 10000),
            'recruiterId' => $this->employee->getId(),
            'source' => "randomSource".rand(0, 10000),
            'company' => "randomCompany".rand(0, 10000),
            'nip' => '5312456586',
            'firmName' => 'Random firm name',
            'firmAddress' => 'Random firm address'
        ];
        $response = $this->request('POST', $url, $this->user, $data);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        /** @var array<int,array<string,mixed>> $json */
        $json = json_decode($response->getContent(), true);
        $this->assertEmpty($json);

        $this->potentialEmployee = $this->em->getRepository('AdventureBundle:PotentialEmployee')->findOneBy([
            'name' => $data['name'],
            'lastName' => $data['lastName']
        ]);
        $this->assertNotNull($this->potentialEmployee);
    }
}