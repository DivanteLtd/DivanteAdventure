<?php

namespace Divante\Bundle\AdventureBundle\Entity\Repository;

use Divante\Bundle\AdventureBundle\Entity\ConfigEntry;
use Doctrine\ORM\EntityRepository;

class ConfigEntryRepository extends EntityRepository
{
    /**
     * @return ConfigEntry[]
     */
    public function getConfigValues() : array
    {
        $builder = $this->createQueryBuilder('ce');
        $query = $builder
                    ->where('ce.replacedAt is null')
                    ->andWhere('ce.group != :content')
                    ->setParameter('content', 'content')
                    ->getQuery();
        return $query->getResult();
    }
}
