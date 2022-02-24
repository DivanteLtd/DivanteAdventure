<?php

namespace Divante\Bundle\AdventureBundle\Entity\Repository;

use Divante\Bundle\AdventureBundle\Entity\PotentialEmployee;
use Doctrine\ORM\EntityRepository;

class PotentialEmployeeRepository extends EntityRepository
{
    public function findOneByEmailAddressUsername(string $email) : ?PotentialEmployee
    {
        preg_match('/^([^@]*@).*$/', $email, $matches);
        if (count($matches) !== 2) {
            return null;
        }
        $emailUsername = $matches[1].'%';
        /** @var PotentialEmployee[] $result */
        $result = $this->createQueryBuilder('e')
            ->where('e.email LIKE :email')
            ->setParameter('email', $emailUsername)
            ->getQuery()
            ->getResult();
        if (count($result) === 0) {
            return null;
        }
        return $result[0];
    }
}
