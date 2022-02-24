<?php


namespace Divante\Bundle\AdventureBundle\MessageHandler\Employee;

use Divante\Bundle\AdventureBundle\Entity\Contract;
use Divante\Bundle\AdventureBundle\Entity\ContractType;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Message\Employee\AddContract;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use DateTime;

class AddContractHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param AddContract $message
     * @throws \Exception
     */
    public function __invoke(AddContract $message) :void
    {
        $employee = $this->getEmployee($message->getEmployeeId());
        $contractType = $this->getContractType($message->getTypeId());
        $contract = new Contract();
        $contract->setType($contractType);
        $contract->setEmployee($employee);
        $contract->setActive($message->isActive());
        if (is_null($message->getNoticePeriod())) {
            $contract->setNoticePeriod(null);
        } else {
            $contract->setNoticePeriod((int)$message->getNoticePeriod());
        }
        $contract->setStartDate((new DateTime($message->getStartDate())));
        if (!empty($message->getEndDate())) {
            $contract->setEndDate((new DateTime($message->getEndDate())));
        }
        $contract->setAssignDate((new DateTime($message->getAssignDate())));
        $contract->setCreatedAt();
        $contract->setUpdatedAt();
        $this->em->beginTransaction();
        try {
            $this->em->persist($contract);
            $this->em->flush();
            $this->em->commit();
        } catch (\Exception $exception) {
            $this->em->rollback();
            throw $exception;
        }
    }

    private function getEmployee(int $id) :Employee
    {
        $employee = $this->em->getRepository(Employee::class)->find($id);
        if (is_null($employee)) {
            throw new NotFoundHttpException("Employee with id $id has not been found");
        }
        return $employee;
    }

    private function getContractType(int $id) :ContractType
    {
        $contractType = $this->em->getRepository(ContractType::class)->find($id);
        if (is_null($contractType)) {
            throw new NotFoundHttpException("Contract Type with id $id has not been found");
        }
        return $contractType;
    }
}
