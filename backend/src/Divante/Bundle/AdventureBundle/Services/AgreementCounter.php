<?php

namespace Divante\Bundle\AdventureBundle\Services;

use Divante\Bundle\AdventureBundle\Entity\Agreement;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Doctrine\ORM\EntityManagerInterface;

class AgreementCounter
{
    private EntityManagerInterface $entityManager;
    /** @var Agreement[] */
    private static array $requiredAgreements = [];

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getRequiredAgreementsCount(Employee $employee) : int
    {
        if (empty(self::$requiredAgreements)) {
            $agreementRepo = $this->entityManager->getRepository(Agreement::class);
            self::$requiredAgreements = $agreementRepo->findBy([
                'required' => 1,
            ]);
        }
        $filter = fn(Agreement $agreement) : bool => $this->filterAgreement($agreement, $employee);
        return count(array_filter(self::$requiredAgreements, $filter));
    }

    public function filterAgreement(Agreement $agreement, Employee $employee) : bool
    {
        if (!$agreement->isRequired()) {
            return false;
        }
        $contract = $employee->getContractId();
        if (is_null($contract)) {
            return false;
        }
        return in_array($contract, $agreement->getContractIds());
    }
}
