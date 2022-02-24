<?php

namespace Tests\Entrypoints\Api\Period;

use DateTime;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\LeavePeriod;
use Tests\Entrypoints\AbstractEntrypointTest;

abstract class AbstractPeriodTest extends AbstractEntrypointTest
{
    public function generatePeriod(Employee $owner) : LeavePeriod
    {
        $period = new LeavePeriod();
        $period->setDateFrom(new DateTime())
            ->setDateTo(new DateTime())
            ->setEmployee($owner)
            ->setFreedays(rand(0, 20))
            ->setSickLeaveDays(rand(0, 20))
            ->setCreatedAt()
            ->setUpdatedAt();
        $this->em->persist($period);
        return $period;
    }
}