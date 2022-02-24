<?php

namespace Divante\Bundle\AdventureBundle\Mappers\Employee;

use DateTime;
use Divante\Bundle\AdventureBundle\Entity\Employee;

class HrEmployeeMapper extends AbstractEmployeeMapper
{
    private UserEmployeeMapper $userEmployeeMapper;

    public function __construct(UserEmployeeMapper $userEmployeeMapper)
    {
        $this->userEmployeeMapper = $userEmployeeMapper;
    }

    /** @inheritDoc */
    public function mapEmployeeToJson(Employee $entity): array
    {
        return array_merge(
            $this->userEmployeeMapper->mapEmployeeToJson($entity),
            [
                'hiredAt' => $this->getDate($entity->getHiredAt()),
                'hiredTo' => $this->getDate($entity->getHiredTo()),
                'dateOfBirth' => $this->getDate($entity->getDateOfBirth()),
                "city" => $entity->getCity(),
                "street" => $entity->getStreet(),
                "postalCode" => $entity->getPostalCode(),
                "country" => $entity->getCountry(),
                'contract' => [
                    'id' => $entity->getContractId(),
                    'name' => Employee::getContractNameById($entity->getContractId()),
                ],
                'privatePhone' => $entity->getPrivatePhone(),
            ]
        );
    }

    private function getDate(?DateTime $dateTime) : ?string
    {
        return is_null($dateTime) ? null : $dateTime->format('Y-m-d');
    }
}
