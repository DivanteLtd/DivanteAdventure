<?php


namespace Divante\Bundle\AdventureBundle\Mappers\Employee;

use Divante\Bundle\AdventureBundle\Entity\Contract;

class ContractMapper
{
    public function mapContractToJson(Contract $contract) :array
    {
        $endDate = '';
        if (!is_null($contract->getEndDate())) {
            $endDate = $contract->getEndDate()->format('Y-m-d H:i:s');
        }
        return [
            "id" => $contract->getId(),
            "employeeId" => $contract->getEmployee()->getId(),
            "contractType" => $contract->getType()->getName(),
            "contractTypeId" => $contract->getType()->getId(),
            "startDate" => $contract->getStartDate()->format('Y-m-d H:i:s'),
            "endDate" => $endDate,
            "assignDate" => $contract->getAssignDate()->format('Y-m-d H:i:s'),
            "noticePeriod" => $contract->getNoticePeriod(),
        ];
    }
}
