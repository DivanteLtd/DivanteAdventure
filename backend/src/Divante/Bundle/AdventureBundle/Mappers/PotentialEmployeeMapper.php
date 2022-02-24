<?php

namespace Divante\Bundle\AdventureBundle\Mappers;

use Divante\Bundle\AdventureBundle\Entity\PotentialEmployee;
use Divante\Bundle\AdventureBundle\Mappers\Employee\HrEmployeeMapper;

class PotentialEmployeeMapper implements Mapper
{
    private PositionMapper $positionMapper;
    private TribeMapper $tribeMapper;
    private HrEmployeeMapper $employeeMapper;

    public function __construct(
        PositionMapper $positionMapper,
        TribeMapper $tribeMapper,
        HrEmployeeMapper $hrEmployeeMapper
    ) {
        $this->positionMapper = $positionMapper;
        $this->tribeMapper = $tribeMapper;
        $this->employeeMapper = $hrEmployeeMapper;
    }

    /**
     * @param PotentialEmployee $entity
     * @return array<string,mixed>
     */
    public function mapEntity($entity): array
    {
        $result = [
            'id' => $entity->getId(),
            'createdAt' => $entity->getCreatedAt()->getTimestamp(),
            'updatedAt' => $entity->getUpdatedAt()->getTimestamp(),
            'name' => $entity->getName(),
            'lastName' => $entity->getLastName(),
            'email' => $entity->getEmail(),
            'contractType' => $entity->getContractType(),
            'status' => $entity->getStatus(),
            'gender' => $entity->getGender(),
            'dateOfBirth' => $entity->getDateOfBirth()->format('Y-m-d'),
            'rejectionCause' => $entity->getRejectionCause(),
            'privatePhone' => $entity->getPrivatePhone(),
            'city' => $entity->getCity(),
            'postalCode' => $entity->getPostalCode(),
            'street' => $entity->getStreet(),
            'country' => $entity->getCountry(),
            'privateEmail' => $entity->getPrivateEmail(),
            'source' => $entity->getSource(),
            'company' => $entity->getCompany(),
            'nip' => $entity->getNip(),
            'firmName' => $entity->getFirmName(),
            'firmAddress' => $entity->getFirmAddress(),
            'language' => $entity->getLanguage(),
        ];
        $employee = $entity->getRecruiter();
        if (!is_null($employee)) {
            $result['recruiter'] = [
                'id' => $employee->getId(),
                'photo' => $employee->getPhoto(),
                'name' => $employee->getName(),
                'lastName' => $employee->getLastName(),
            ];
        }
        if (!is_null($entity->getDesignatedHireDate())) {
            $result['hireDate'] = $entity->getDesignatedHireDate()->format('Y-m-d');
        }
        if (!is_null($entity->getDesignatedPosition())) {
            $result['position'] = $this->positionMapper->mapEntity($entity->getDesignatedPosition());
        }
        if (!is_null($entity->getDesignatedTribe())) {
            $result['tribe'] = $this->tribeMapper->mapEntity($entity->getDesignatedTribe());
        }
        if (!is_null($entity->getJoinedEmployee())) {
            $result['joinedEmployee'] = $this->employeeMapper->mapEmployeeToJson($entity->getJoinedEmployee());
        }
        if (!is_null($entity->getWelcomeDayDate())) {
            $result['welcomeDay'] = $entity->getWelcomeDayDate()->format('Y-m-d');
        }
        return $result;
    }

    /**
     * @param PotentialEmployee $employee
     * @return array<string,mixed>
     */
    public function __invoke(PotentialEmployee $employee) : array
    {
        return $this->mapEntity($employee);
    }
}
