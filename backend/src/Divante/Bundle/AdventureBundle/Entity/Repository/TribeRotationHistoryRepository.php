<?php

namespace Divante\Bundle\AdventureBundle\Entity\Repository;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use \Divante\Bundle\AdventureBundle\Entity\TribeRotationHistory;
use Doctrine\ORM\EntityRepository;

class TribeRotationHistoryRepository extends EntityRepository
{
    /** @return mixed */
    public function deleteAll()
    {
        $qb = $this->createQueryBuilder('tribe_rotation_history');
        return $qb->delete()->getQuery()->execute();
    }

    public function findOneRecordByEmployeeAndDate(Employee $employee, ?\DateTime $dateTime) : ?TribeRotationHistory
    {
        if (is_null($dateTime)) {
            return null;
        }
        return $this->findOneBy([
            'tribeName' => $employee->getTribe()->getName(),
            'month' => $dateTime->format('m'),
            'year' => $dateTime->format('Y')
        ]);
    }
}
