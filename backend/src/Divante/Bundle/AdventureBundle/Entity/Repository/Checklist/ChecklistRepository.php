<?php

namespace Divante\Bundle\AdventureBundle\Entity\Repository\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Checklist\Checklist;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Doctrine\ORM\EntityRepository;

class ChecklistRepository extends EntityRepository
{
    /**
     * @param Employee $employee
     * @return Checklist[]
     */
    public function findByOwnerOrSubject(Employee $employee) : array
    {
        $qb = $this->createQueryBuilder('c');
        $query = $qb
            ->from('AdventureBundle:Checklist\Checklist', 'cl')
            ->select('cl')
            ->leftJoin('cl.owners', 'owners')
            ->where('owners.id = :employee')
            ->orWhere('cl.subject = :employee')
            ->setParameter(':employee', $employee)
            ->getQuery();
        return $query->getResult();
    }
}
