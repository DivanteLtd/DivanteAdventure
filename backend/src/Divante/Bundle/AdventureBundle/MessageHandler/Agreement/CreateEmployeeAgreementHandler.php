<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 * Date: 26.03.19
 * Time: 08:29
 */

namespace Divante\Bundle\AdventureBundle\MessageHandler\Agreement;

use Divante\Bundle\AdventureBundle\Entity\Agreement;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\EmployeeAgreement;
use Divante\Bundle\AdventureBundle\Message\Agreement\CreateEmployeeAgreement;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class CreateEmployeeAgreementHandler
{
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param CreateEmployeeAgreement $message
     * @throws Exception
     */
    public function __invoke(CreateEmployeeAgreement $message) : void
    {
        $em = $this->em;

        $employeeId = $message->getEmployeeId();
        $agreementId = $message->getAgreementId();
        if ($employeeId === -1) {
            throw new Exception("employeeId parameter is required");
        }
        if ($agreementId === -1) {
            throw new Exception("agreementId parameter is required");
        }

        /** @var Employee|null $employee */
        $employee = $em->getRepository(Employee::class)->find($employeeId);
        /** @var Agreement|null $agreement */
        $agreement = $em->getRepository(Agreement::class)->find($agreementId);

        if (is_null($employee)) {
            throw new Exception("Employee with id $employeeId not found.");
        }
        if (is_null($agreement)) {
            throw new Exception("Agreement with id $agreementId not found.");
        }

        $em->getConnection()->beginTransaction();
        try {
            $employeeAgreement = new EmployeeAgreement();
            $employeeAgreement
                ->setEmployee($employee)
                ->setName($employee->getName())
                ->setLastName($employee->getLastName())
                ->setEmail($employee->getEmail())
                ->setAgreement($agreement)
                ->setCreatedAt()
                ->setUpdatedAt();
            $em->persist($employeeAgreement);
            $em->flush();
            $em->getConnection()->commit();
        } catch (Exception $e) {
            $em->getConnection()->rollBack();
        }
    }
}
