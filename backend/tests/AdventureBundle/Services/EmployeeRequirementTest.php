<?php

namespace Tests\AdventureBundle\Services;

use Divante\Bundle\AdventureBundle\Entity\Agreement;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\EmployeeAgreement;
use Divante\Bundle\AdventureBundle\Services\AgreementCounter;
use Divante\Bundle\AdventureBundle\Services\EmployeeRequirement;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\FoundationTestCase;

class EmployeeRequirementTest extends FoundationTestCase
{
    public function testPinNotSet() : void
    {
        $employee = new Employee();
        $employeeRequirement = $this->getEmployeeRequirement();
        $hasSet = $employeeRequirement->hasSetPin($employee);
        $this->assertFalse($hasSet);
    }

    public function testPinSet() : void
    {
        $employee = new Employee();
        $employee->setAndHashPin("1234");
        $employeeRequirement = $this->getEmployeeRequirement();
        $hasSet = $employeeRequirement->hasSetPin($employee);
        $this->assertTrue($hasSet);
    }

    public function testNotEnoughAgreementsAccepted() : void
    {
        $acceptedAgreements = 5;
        $requiredAgreements = 10;

        $employee = new Employee();
        $contractId = rand(1, 3);
        $employee->setContractId($contractId);
        for ($i = 0; $i < $acceptedAgreements; $i++) {
            $agreement = new Agreement();
            $agreement->setContractIds([$contractId]);
            $agreement->setRequired(true);
            $pairing = new EmployeeAgreement();
            $pairing
                ->setAgreement($agreement)
                ->setEmployee($employee);
            $employee->getAgreements()->add($pairing);
        }

        $requirement = $this->getEmployeeRequirement($requiredAgreements);
        $result = $requirement->hasRequiredAgreements($employee);
        $this->assertFalse($result);
    }

    public function testEnoughAgreementsAccepted() : void
    {
        $acceptedAgreements = 10;
        $requiredAgreements = 10;

        $employee = new Employee();
        $contractId = rand(1, 3);
        $employee->setContractId($contractId);
        for ($i = 0; $i < $acceptedAgreements; $i++) {
            $agreement = new Agreement();
            $agreement->setContractIds([$contractId]);
            $agreement->setRequired(true);
            $pairing = new EmployeeAgreement();
            $pairing
                ->setAgreement($agreement)
                ->setEmployee($employee);
            $employee->getAgreements()->add($pairing);
        }

        $requirement = $this->getEmployeeRequirement($requiredAgreements);
        $result = $requirement->hasRequiredAgreements($employee);
        $this->assertTrue($result);
    }

    private function getEmployeeRequirement(?int $returnedAgreementsCount = null) : EmployeeRequirement
    {
        /** @var AgreementCounter|MockObject $agreementCounter */
        $agreementCounter = $this->getMockBuilder(AgreementCounter::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRequiredAgreementsCount'])
            ->getMock();

        if (!is_null($returnedAgreementsCount)) {
            $agreementCounter
                ->expects($this->any())
                ->method('getRequiredAgreementsCount')
                ->willReturn($returnedAgreementsCount);
        }
        return new EmployeeRequirement($agreementCounter);
    }
}
