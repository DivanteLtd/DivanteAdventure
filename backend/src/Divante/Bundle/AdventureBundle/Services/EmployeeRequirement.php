<?php

namespace Divante\Bundle\AdventureBundle\Services;

use Divante\Bundle\AdventureBundle\Entity\Agreement;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\EmployeeAgreement;

class EmployeeRequirement
{
    private AgreementCounter $counter;

    public function __construct(AgreementCounter $counter)
    {
        $this->counter = $counter;
    }

    public function hasSetPin(Employee $employee) : bool
    {
        return !is_null($employee->getHashedPin());
    }

    public function hasRequiredAgreements(Employee $employee) : bool
    {
        $markedRequiredAgreementsCount = $employee->getAgreements()
            ->map(fn(EmployeeAgreement $pair) : Agreement => $pair->getAgreement())
            ->filter(fn(Agreement $agreement) : bool => $this->counter->filterAgreement($agreement, $employee))
            ->count();
        $requiredAgreementsCount = $this->counter->getRequiredAgreementsCount($employee);
        return $markedRequiredAgreementsCount >= $requiredAgreementsCount;
    }
}
