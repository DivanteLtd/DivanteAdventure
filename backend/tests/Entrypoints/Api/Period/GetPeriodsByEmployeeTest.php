<?php

namespace Tests\Entrypoints\Api\Period;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\LeavePeriod;
use Divante\Bundle\AdventureBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class GetPeriodsByEmployeeTest extends AbstractPeriodTest
{
    private ?User $user = null;
    private ?Employee $employee = null;
    private ?LeavePeriod $leavePeriod = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->generateFosUser(['ROLE_MANAGER']);
        $this->employee = $this->generateEmployee($this->user);
        $this->leavePeriod = $this->generatePeriod($this->employee);
        $this->em->flush();
    }

    protected function tearDown(): void
    {
        $this->em->remove($this->leavePeriod);
        $this->em->remove($this->employee);
        $this->em->remove($this->user);
        $this->em->flush();
        parent::tearDown();
    }

    public function testEntrypoint() : void
    {
        $url = '/api/period/'.$this->employee->getId();
        $response = $this->request('GET', $url, $this->user);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        /** @var array<int,array<string,mixed>> $json */
        $json = json_decode($response->getContent(), true);
        $this->assertCount(1, $json);

        /** @var array<string,mixed> $entry */
        $entry = array_pop($json);
        $this->assertArrayHasKey('id', $entry);
        $this->assertEquals($this->leavePeriod->getId(), $entry['id']);
    }
}