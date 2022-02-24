<?php

namespace Tests\Entrypoints\Api\Employees;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\EmployeeEndCooperation;
use Divante\Bundle\AdventureBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class AddEmployeeEndingCooperationTest extends AbstractEmployeesTest
{
    private ?User $user = null;
    private ?Employee $employee = null;
    private ?EmployeeEndCooperation $endCooperation = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->generateFosUser(['ROLE_HR']);
        $this->employee = $this->generateEmployee($this->user, null, rand(1, 3));
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
        $whoEndedCooperation = "RandomEnd".rand(0, 10000);
        $url = '/api/employees/endedWork';
        $response = $this->request("POST", $url, $this->user, [
            'email' => $this->employee->getEmail(),
            'whoEndedCooperation' => $whoEndedCooperation,
            'exitInterview' => true,
            'checklist' => true,
            'dismiss' => '2019-12-31',
        ]);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        /** @var array<int,array<string,mixed>> $json */
        $json = json_decode($response->getContent(), true);
        $this->assertEmpty($json);
        /** @var EmployeeEndCooperation|null $end */
        $end = $this->em->getRepository('AdventureBundle:EmployeeEndCooperation')->findOneBy([
            'employee' => $this->employee,

        ]);
        $this->assertNotNull($end);
        $this->assertEquals($this->employee->getName(), $end->getName());
        $this->assertEquals($whoEndedCooperation, $end->getWhoEndedCooperation());
        $this->endCooperation = $end;
    }
}
