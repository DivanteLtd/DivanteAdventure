<?php

namespace Divante\Bundle\AdventureBundle\Entity\Repository;

use Divante\Bundle\AdventureBundle\Entity\PublicHoliday;
use Doctrine\ORM\EntityRepository;

class PublicHolidayRepository extends EntityRepository
{
    /**
     * @param int $startYear
     * @param int $endYear
     * @return PublicHoliday[]
     */
    public function getDaysBetweenYears(int $startYear, int $endYear): array
    {
        $builder = $this->createQueryBuilder('ph');
        $builder
            ->andWhere('ph.enabled = TRUE')
            ->andWhere(
                $builder->expr()->orX(
                    $builder->expr()->andX(
                        $builder->expr()->isNotNull('ph.date'),
                        $builder->expr()->between('YEAR(ph.date)', ':start', ':end')
                    ),
                    $builder->expr()->eq("ph.repeating", "TRUE"),
                    $builder->expr()->isNotNull('ph.calculationType'),
                )
            )
            ->setParameter('start', $startYear)
            ->setParameter('end', $endYear);
        $query = $builder->getQuery();
        return $query->getResult();
    }
}
