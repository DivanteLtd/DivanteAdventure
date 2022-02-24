<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\Mappers\Hardware;

use Divante\Bundle\AdventureBundle\Entity\Hardware\HardwareAgreement;
use Divante\Bundle\AdventureBundle\Entity\Hardware\HardwareAssignment;
use Divante\Bundle\AdventureBundle\Mappers\Mapper;

class HardwareGenerateMapper implements Mapper
{
    /**
     * @param HardwareAgreement $hardwareAgreement
     * @return array<string,mixed>
     */
    public function mapEntity($hardwareAgreement): array
    {
        return array_merge(
            $this->getAgreementData($hardwareAgreement),
            $this->getHardwareData($hardwareAgreement->getAssignment()),
            $this->getEmployeeData($hardwareAgreement),
        );
    }

    /**
     * @param HardwareAgreement $hardwareAgreement
     * @return array<string,mixed>
     */
    private function getAgreementData($hardwareAgreement): array
    {
        return [
            'id' => $hardwareAgreement->getId(),
            'signedByHelpdesk' => $hardwareAgreement->getSignedByHelpdesk() ?? '',
            'signedByUser' => $hardwareAgreement->getSignedByUser() ?? '',
            'generated' => $hardwareAgreement->getGenerationDate()
                ? $hardwareAgreement->getGenerationDate()->format('Y-m-d') : null,
            'createdAt' => $hardwareAgreement->getCreatedAt()->format('Y-m-d'),
            'languages' => $hardwareAgreement->getUseLanguages(),
        ];
    }

    /**
     * @param HardwareAssignment|null $assignment
     * @return array<string,string>
     */
    private function getHardwareData(?HardwareAssignment $assignment) : array
    {
        if (is_null($assignment)) {
            return [];
        }
        return [
            'category' => $assignment->getCategory(),
            'manufacturer' => $assignment->getManufacturer(),
            'model' => $assignment->getModel(),
            'serialNumber' => $assignment->getSerialNumber(),
        ];
    }

    /**
     * @param HardwareAgreement $agreement
     * @return array<string,string>
     */
    private function getEmployeeData(HardwareAgreement $agreement) : array
    {
        $data = [
            'name' => $agreement->getName(),
            'lastName' => $agreement->getLastName(),
        ];
        $employee = $agreement->getAssignment()->getEmployee();
        $potentialEmployee = $agreement->getAssignment()->getPotentialEmployee();
        $contract = !is_null($employee) ? $employee->getContractType() : $potentialEmployee->getContractType();
        $data['contract'] = $contract;
        return $data;
    }
}
