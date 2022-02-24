<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Employee;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\EmployeeEndCooperation;
use Divante\Bundle\AdventureBundle\Message\Employee\CreateEmployeeEndingCooperation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CreateEmployeeEndingCooperationHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param CreateEmployeeEndingCooperation $message
     * @throws \Exception
     */
    public function __invoke(CreateEmployeeEndingCooperation $message) : void
    {
        $em = $this->em;
        $employee = $this->getEmployee($message->getEmail());
        $em->beginTransaction();
        try {
            $endingCooperation = $this->getEmployeeEndCooperation($employee)
                ->setComment($message->getComment())
                ->setExitInterview($message->isExitInterview())
                ->setChecklist($message->isChecklist())
                ->setNextCompany($message->getNextCompany())
                ->setWhoEndedCooperation($message->getWhoEndedCooperation())
                ->setDismissDate(\DateTime::createFromFormat('Y-m-d', $message->getDismissDate()))
                ->setUpdatedAt();
            $employee
                ->setHiredTo(new \DateTime($message->getDismissDate()))
                ->setUpdatedAt();

            $em->persist($endingCooperation);
            $em->flush();
            $em->commit();
        } catch (\Exception $e) {
            $em->rollback();
            throw new \Exception($e);
        }
    }

    private function getEmployee(string $email): Employee
    {
        /** @var Employee|null $employee */
        $employee = $this->em->getRepository(Employee::class)->findOneBy([
            'email' => $email,
        ]);
        if (is_null($employee)) {
            throw new NotFoundHttpException("Employee with e-mail $email has not been found");
        }
        return $employee;
    }

    private function getEmployeeEndCooperation(Employee $employee): EmployeeEndCooperation
    {
        $position = '';
        if (!is_null($employee->getPosition())) {
            $position = $employee->getPosition()->getName();
        }
        /** @var EmployeeEndCooperation|null $employeeEndCooperation */
        $employeeEndCooperation = $this->em->getRepository(EmployeeEndCooperation::class)->findOneBy([
            'employee' => $employee,
        ]);
        if (is_null($employeeEndCooperation)) {
            $employeeEndCooperation = (new EmployeeEndCooperation())
                ->setEmployee($employee)
                ->setName($employee->getName())
                ->setLastName($employee->getLastName())
                ->setPosition($position)
                ->setCreatedAt();
        }
        return $employeeEndCooperation;
    }
}
