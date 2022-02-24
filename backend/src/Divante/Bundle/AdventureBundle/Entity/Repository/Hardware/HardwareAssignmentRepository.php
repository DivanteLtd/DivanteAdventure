<?php

namespace Divante\Bundle\AdventureBundle\Entity\Repository\Hardware;

use Divante\Bundle\AdventureBundle\Entity\Data\ContractOwner;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\PotentialEmployee;
use Doctrine\ORM\EntityRepository;

class HardwareAssignmentRepository extends EntityRepository
{
    public function deleteUnassignedAssets(ContractOwner $user, array $idsHardware) :void
    {
        $q = $this->createQueryBuilder('hardware_assignment');
        $q->andWhere(
            $q->expr()->notIn('hardware_assignment.assetId', ':ids')
        )->setParameter('ids', $idsHardware);
        if ($user instanceof Employee) {
            $q->andWhere(
                $q->expr()->eq('hardware_assignment.employee', ':employee')
            )->setParameter('employee', $user);
        }
        if ($user instanceof PotentialEmployee) {
            $q->andWhere(
                $q->expr()->eq('hardware_assignment.potentialEmployee', ':employee')
            )->setParameter('employee', $user);
        }

        $q->delete()->getQuery()->execute();
    }
}
