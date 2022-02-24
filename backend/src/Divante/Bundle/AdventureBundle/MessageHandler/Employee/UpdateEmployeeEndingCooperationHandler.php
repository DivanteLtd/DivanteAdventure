<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Employee;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\EmployeeEndCooperation;
use Divante\Bundle\AdventureBundle\Message\Employee\UpdateEmployeeEndingCooperation;
use Doctrine\ORM\EntityManagerInterface;

class UpdateEmployeeEndingCooperationHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param UpdateEmployeeEndingCooperation $message
     * @throws \Exception
     */
    public function __invoke(UpdateEmployeeEndingCooperation $message) : void
    {
        $em = $this->em;
        /** @var EmployeeEndCooperation|null $employeeEndCooperation */
        $employeeEndCooperation = $em->getRepository(EmployeeEndCooperation::class)
            ->find($message->getId());
        /** @var Employee $employee */
        $employee = $em->getRepository(Employee::class)->find($message->getEmployeeId());
        $em->beginTransaction();
        try {
            $employee
                ->setHiredTo($message->getDismissDate())
                ->setUpdatedAt();

            $employeeEndCooperation->setEmployee($employee)
                ->setDismissDate($message->getDismissDate())
                ->setComment($message->getComment())
                ->setExitInterview($message->isExitInterview())
                ->setChecklist($message->isChecklist())
                ->setNextCompany($message->getNextCompany())
                ->setWhoEndedCooperation($message->getWhoEndedCooperation())
                ->setCreatedAt()
                ->setUpdatedAt();
            $em->persist($employeeEndCooperation);
            $em->flush();
            $em->commit();
        } catch (\Exception $e) {
            $em->rollback();
            throw new \Exception($e);
        }
    }
}
