<?php

namespace Divante\Bundle\AdventureBundle\Entity\Repository\Hardware;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Hardware\HardwareAgreement;
use Divante\Bundle\AdventureBundle\Infrastructure\Config\EmailConfig;
use Doctrine\ORM\EntityRepository;

class HardwareAgreementRepository extends EntityRepository
{
    /** @return HardwareAgreement[] */
    public function getUnsignedAgreements() : array
    {
        return $this->createQueryBuilder('a')
            ->where('a.signedByHelpdesk is null')
            ->orWhere('a.signedByUser is null')
            ->getQuery()
            ->execute();
    }

    /**
     * @param Employee $user
     * @param EmailConfig $config
     * @return HardwareAgreement[]
     */
    public function getAgreementsForUser(Employee $user, EmailConfig $config) : array
    {
        $result = $this->createQueryBuilder('a')
            ->join('a.assignment', 's')
            ->where('a.signedByHelpdesk is not null')
            ->andWhere('a.signedByUser is null')
            ->andWhere('a.generated is not null')
            ->andWhere('s.employee = :employee')
            ->setParameter('employee', $user)
            ->getQuery()
            ->execute();
        if ($user->getEmail() === $config->getHelpdeskResponsibleEmail()) {
            $result = [
                ...$result,
                ...$this->createQueryBuilder('a')
                    ->where('a.signedByHelpdesk is null')
                    ->andWhere('a.generated is not null')
                    ->getQuery()
                    ->execute()
            ];
        }
        return $result;
    }
}
