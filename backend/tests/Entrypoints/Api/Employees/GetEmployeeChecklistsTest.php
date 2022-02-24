<?php

namespace Tests\Entrypoints\Api\Employees;

use Divante\Bundle\AdventureBundle\Entity\Checklist\Checklist;
use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistInterface;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class GetEmployeeChecklistsTest extends AbstractEmployeesTest
{
    private ?User $user = null;
    private ?Employee $employee = null;
    private ?Checklist $checklist = null;


    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->generateFosUser(['ROLE_SUPER_ADMIN']);
        $this->employee = $this->generateEmployee($this->user);
        $this->checklist = new Checklist();
        $this->checklist
            ->setCreatedAt()
            ->setUpdatedAt()
            ->setStartedAt(new \DateTime())
            ->setDueDate(new \DateTime())
            ->setNameEn("RandomName".rand(0, 10000))
            ->setNamePl("RandomName".rand(0, 10000))
            ->setType(ChecklistInterface::TYPE_DISTRIBUTED)
            ->setSubject($this->employee);
        $this->em->persist($this->checklist);
        $this->em->flush();
    }

    protected function tearDown() : void
    {
        $this->em->remove($this->checklist);
        $this->em->remove($this->employee);
        $this->em->remove($this->user);
        $this->em->flush();
        parent::tearDown();
    }

    public function testEntrypoint() : void
    {
        $url = '/api/employees/'.$this->employee->getId().'/checklists';
        $response = $this->request("GET", $url, $this->user);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        /** @var array<int,array<string,mixed>> $json */
        $json = json_decode($response->getContent(), true);
        $this->assertCount(1, $json);
        /** @var array<string,mixed> $entry */
        $entry = array_pop($json);
        $this->assertArrayHasKey('namePl', $entry);
        $this->assertEquals($this->checklist->getNamePl(), $entry['namePl']);
    }
}
