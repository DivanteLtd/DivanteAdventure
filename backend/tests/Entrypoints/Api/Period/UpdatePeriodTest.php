<?php

namespace Tests\Entrypoints\Api\Period;

use DateTime;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\LeavePeriod;
use Divante\Bundle\AdventureBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class UpdatePeriodTest extends AbstractPeriodTest
{
    private ?User $user = null;
    private ?Employee $employee = null;
    private ?LeavePeriod $leavePeriod = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->generateFosUser(['ROLE_SUPER_ADMIN']);
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
        $url = '/api/period/'.$this->leavePeriod->getId();
        $dateFrom = new DateTime("2000-01-01");
        $dateTo = new DateTime("2000-12-31");
        $data = [
            'dateFrom' => $dateFrom->format('Y-m-d'),
            'dateTo' => $dateTo->format('Y-m-d'),
            'sickLeaveDays' => 0,
            'freeDays' => 0,
            'employeeId' => $this->employee->getId(),
        ];
        $this->assertNotEquals($dateFrom, $this->leavePeriod->getDateFrom());
        $response = $this->request('PATCH', $url, $this->user, $data);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->em->refresh($this->leavePeriod);
        $this->assertEquals($dateFrom, $this->leavePeriod->getDateFrom());
    }
}