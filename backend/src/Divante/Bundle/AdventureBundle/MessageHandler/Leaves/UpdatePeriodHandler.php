<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 13.02.19
 * Time: 11:57
 */

namespace Divante\Bundle\AdventureBundle\MessageHandler\Leaves;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\LeavePeriod;
use Divante\Bundle\AdventureBundle\Message\Leaves\UpdatePeriod;
use Doctrine\ORM\EntityManagerInterface;

class UpdatePeriodHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param UpdatePeriod $message
     * @throws \Exception
     */
    public function __invoke(UpdatePeriod $message) : void
    {
        $em = $this->em;

        /** @var LeavePeriod|null $period */
        $period = $em->getRepository(LeavePeriod::class)->find($message->getPeriodId());
        if (is_null($period)) {
            throw new \Exception("Period with given ID not found.");
        }

        $sickLeaveDays = $message->getSickLeaveDays();
        $freeDays = $message->getFreeDays();
        $employeeId = $message->getEmployeeId();
        $dateFrom = $message->getDateFrom();
        $dateFromObj = is_null($dateFrom) ? null : new \DateTime($dateFrom);
        $dateTo = $message->getDateTo();
        $dateToObj = is_null($dateTo) ? null : new \DateTime($dateTo);
        $comment = $message->getComment();

        if (!is_null($sickLeaveDays) && $sickLeaveDays < 0) {
            throw new \Exception("sickLeaveDays must be greater than or equal to zero.");
        }
        if (!is_null($freeDays) && $freeDays < 0) {
            throw new \Exception("freeDays must be greater than or equal to zero.");
        }
        if (!is_null($employeeId) && $employeeId < 0) {
            throw new \Exception("Incorrect employeeId");
        }

        /** @var Employee|null $employee */
        $employee = null;
        if (!is_null($employeeId)) {
            /** @var Employee|null $employee */
            $employee = $em
                ->getRepository(Employee::class)
                ->find($message->getEmployeeId());
            if (is_null($employee)) {
                throw new \Exception("There is no employee with ID ".$message->getEmployeeId());
            }
        }

        $em->getConnection()->beginTransaction();
        try {
            $period
                ->setFreedays($freeDays ?? $period->getFreedays())
                ->setSickLeaveDays($sickLeaveDays ?? $period->getSickLeaveDays())
                ->setEmployee($employee ?? $period->getEmployee())
                ->setDateFrom($dateFromObj ?? $period->getDateFrom())
                ->setDateTo($dateToObj ?? $period->getDateTo())
                ->setCommentSystem($comment ?? $period->getCommentSystem())
                ->setUpdatedAt();
            $em->persist($period);
            $em->flush();
            $em->getConnection()->commit();
        } catch (\Exception $e) {
            $em->getConnection()->rollBack();
            throw new \Exception("Updating period failed", 0, $e);
        }
    }
}
