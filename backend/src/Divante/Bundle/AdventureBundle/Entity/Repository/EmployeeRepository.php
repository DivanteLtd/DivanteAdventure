<?php

namespace Divante\Bundle\AdventureBundle\Entity\Repository;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Doctrine\ORM\EntityRepository;

class EmployeeRepository extends EntityRepository
{

    /** @return Employee[] */
    public function findAllWithoutFormerEmployees() : array
    {
        return array_filter(
            $this->findAll(),
            function (Employee $employee) {
                return !$employee->isFormer();
            }
        );
    }

    /**
     * @param mixed[] $criteria
     * @param string[]|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return Employee[]
     */
    public function findByWithoutFormerEmployees(
        array $criteria,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null
    ) : array {
        return array_filter(
            $this->findBy($criteria, $orderBy, $limit, $offset),
            function (Employee $employee) {
                return !$employee->isFormer();
            }
        );
    }

    public function findOneByEmailAddressUsername(string $email) : ?Employee
    {
        preg_match('/^([^@]*@).*$/', $email, $matches);
        if (count($matches) !== 2) {
            return null;
        }
        $emailUsername = $matches[1].'%';
        /** @var Employee[] $result */
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

    /** @return Employee[] */
    public function getAll() : array
    {
        return $this->createQueryBuilder('e')
            ->where('e.hiredAt is not null')
            ->andWhere('e.tribe is not null')
            ->getQuery()
            ->execute();
    }

    public function getByEmployeeCode(string $code) :array
    {
        return $this->createQueryBuilder('e')
            ->where('e.hiredAt is not null')
            ->andWhere('e.tribe is not null')
            ->andWhere('e.employeeCode = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->execute();
    }

    public function getEmployeeByCivilLawContract() :array
    {
        return $this->createQueryBuilder('e')
            ->where('e.contract = :clc_lump_sum')
            ->orWhere('e.contract = :clc_hourly')
            ->setParameter('clc_lump_sum', Employee::CONTRACT_CLC_LUMP_SUM)
            ->setParameter('clc_hourly', Employee::CONTRACT_CLC_HOURLY)
            ->getQuery()
            ->execute();
    }
}
