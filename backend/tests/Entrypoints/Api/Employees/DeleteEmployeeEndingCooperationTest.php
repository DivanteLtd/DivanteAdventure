<?php

namespace Tests\Entrypoints\Api\Employees;

use DateTime;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\EmployeeEndCooperation;
use Divante\Bundle\AdventureBundle\Entity\Tribe;
use Divante\Bundle\AdventureBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class DeleteEmployeeEndingCooperationTest extends AbstractEmployeesTest
{
    private ?User $user = null;
    private ?Employee $employee = null;
    private ?Tribe $tribe = null;
    private ?EmployeeEndCooperation $endCooperation = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->generateFosUser(['ROLE_HR']);
        $this->tribe = $this->generateTribe();
        $this->employee = $this->generateEmployee($this->user, $this->tribe, rand(1, 3));
        $this->endCooperation = new EmployeeEndCooperation();
        $this->endCooperation
            ->setEmployee($this->employee)
            ->setName($this->employee->getName())
            ->setLastName($this->employee->getLastName())
            ->setChecklist((bool)rand(0,1))
            ->setExitInterview((bool)rand(0,1))
            ->setNextCompany("RandomCompanyName".rand(0, 10000))
            ->setDismissDate(new DateTime('2019-12-31'))
            ->setCreatedAt()
            ->setUpdatedAt();
        $this->em->persist($this->endCooperation);
        $this->em->flush();
    }

    protected function tearDown() : void
    {
        $this->em->remove($this->endCooperation);
        $this->em->remove($this->employee);
        $this->em->remove($this->tribe);
        $this->em->remove($this->user);
        $this->em->flush();
        parent::tearDown();
    }

    public function testEntrypoint() : void
    {
        $id = $this->endCooperation->getId();
        $url = '/api/employees/endedWork/'.$id;
        $response = $this->request("DELETE", $url, $this->user);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        /** @var array<int,array<string,mixed>> $json */
        $json = json_decode($response->getContent(), true);
        $this->assertEmpty($json);
    }
}
