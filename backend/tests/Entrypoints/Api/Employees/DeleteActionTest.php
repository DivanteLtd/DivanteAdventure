<?php

namespace Tests\Entrypoints\Api\Employees;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Tribe;
use Divante\Bundle\AdventureBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class DeleteActionTest extends AbstractEmployeesTest
{
    private ?User $user = null;
    private ?Employee $employee = null;
    private ?Tribe $tribe = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->generateFosUser(['ROLE_SUPER_ADMIN']);
        $this->tribe = $this->generateTribe();
        $this->employee = $this->generateEmployee($this->user, $this->tribe);
        $this->em->flush();
    }

    protected function tearDown() : void
    {
        $this->em->remove($this->employee);
        $this->em->remove($this->tribe);
        $this->em->remove($this->user);
        $this->em->flush();
        parent::tearDown();
    }

    public function testEntrypoint() : void
    {
        $url = '/api/employees/'.$this->employee->getId();
        $response = $this->request("DELETE", $url, $this->user);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        /** @var array $json */
        $json = json_decode($response->getContent(), true);
        $this->assertEmpty($json);
    }
}
