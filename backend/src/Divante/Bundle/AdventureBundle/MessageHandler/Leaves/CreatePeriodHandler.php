<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 13.02.19
 * Time: 10:55
 */

namespace Divante\Bundle\AdventureBundle\MessageHandler\Leaves;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\LeavePeriod;
use Divante\Bundle\AdventureBundle\Message\Leaves\CreatePeriod;
use Doctrine\ORM\EntityManagerInterface;

class CreatePeriodHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param CreatePeriod $message
     * @throws \Exception
     */
    public function __invoke(CreatePeriod $message) : void
    {
        if ($message->getSickLeaveDays() < 0) {
            throw new \Exception("sickLeaveDays must be greater than or equal to zero.");
        }
        if ($message->getFreeDays() < 0) {
            throw new \Exception("freeDays must be greater than or equal to zero.");
        }
        if ($message->getEmployeeId() < 0) {
            throw new \Exception("Incorrect employeeId");
        }

        /** @var Employee|null $employee */
        $employee = $this->em
            ->getRepository(Employee::class)
            ->find($message->getEmployeeId());
        if (is_null($employee)) {
            throw new \Exception("There is no employee with ID ".$message->getEmployeeId());
        }

        $this->em->getConnection()->beginTransaction();
        try {
            $period = new LeavePeriod();
            $period
                ->setFreedays($message->getFreeDays())
                ->setSickLeaveDays($message->getSickLeaveDays())
                ->setEmployee($employee)
                ->setDateFrom(new \DateTime($message->getDateFrom()))
                ->setDateTo(new \DateTime($message->getDateTo()))
                ->setCreatedAt()
                ->setUpdatedAt();
            $this->em->persist($period);
            $this->em->flush();
            $this->em->getConnection()->commit();
        } catch (\Exception $e) {
            $this->em->getConnection()->rollBack();
            throw new \Exception("Creating period failed", 0, $e);
        }
    }
}
