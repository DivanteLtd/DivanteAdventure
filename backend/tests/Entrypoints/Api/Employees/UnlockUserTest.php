<?php

namespace Tests\Entrypoints\Api\Employees;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class UnlockUserTest extends AbstractEmployeesTest
{
    private ?User $user = null;
    private ?Employee $employee = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->generateFosUser(['ROLE_SUPER_ADMIN']);
        $this->employee = $this->generateEmployee($this->user);
        $this->employee->validatePin("");
        $this->employee->validatePin("");
        $this->employee->validatePin("");
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
        $this->assertTrue($this->employee->isPinLocked());
        $url = '/api/employees/unlock/'.$this->employee->getId();
        $response = $this->request("POST", $url, $this->user);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        /** @var array $json */
        $json = json_decode($response->getContent(), true);
        $this->assertEmpty($json);
        $this->em->refresh($this->employee);
        $this->assertFalse($this->employee->isPinLocked());
    }
}
