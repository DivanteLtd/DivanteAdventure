<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Employee;

use Divante\Bundle\AdventureBundle\Entity\EmployeeEndCooperation;
use Divante\Bundle\AdventureBundle\Message\Employee\DeleteEmployeeEndingCooperation;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class DeleteEmployeeEndingCooperationHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param DeleteEmployeeEndingCooperation $message
     * @throws Exception
     */
    public function __invoke(DeleteEmployeeEndingCooperation $message) : void
    {
        /** @var EmployeeEndCooperation $employeeEndCooperation */
        $employeeEndCooperation = $this->em->getRepository(EmployeeEndCooperation::class)
            ->find($message->getId());
        $employee = $employeeEndCooperation->getEmployee();
        try {
            $this->em->remove($employeeEndCooperation);
            if (!is_null($employee)) {
                $employee->setHiredTo(null);
            }
            $this->em->flush();
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }
}
