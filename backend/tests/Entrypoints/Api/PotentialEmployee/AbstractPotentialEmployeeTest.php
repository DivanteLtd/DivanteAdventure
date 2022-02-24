<?php

namespace Tests\Entrypoints\Api\PotentialEmployee;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\PotentialEmployee;
use Tests\Entrypoints\AbstractEntrypointTest;
use DateTime;

abstract class AbstractPotentialEmployeeTest extends AbstractEntrypointTest
{
    public function generatePotentialEmployee(Employee $recruiter) : PotentialEmployee
    {
        $employee = new PotentialEmployee();
        $employee->setName("RandomName".rand(0, 10000))
            ->setLastName("RandomLastName".rand(0, 10000))
            ->setEmail("randomEmail".rand(0, 10000)."@example.com")
            ->setContractType("randomContract".rand(0, 10000))
            ->setDesignatedTribe(null)
            ->setDesignatedPosition(null)
            ->setDesignatedHireDate(null)
            ->setJoinedEmployee(null)
            ->setStatus(rand(0, 10000))
            ->setRejectionCause(null)
            ->setGender(rand(0, 10000))
            ->setDateOfBirth(new DateTime())
            ->setPrivatePhone(rand(0, 10000))
            ->setPrivateEmail("randomPrivateEmail".rand(0, 10000)."@example.com")
            ->setCity("randomCity".rand(0, 10000))
            ->setPostalCode("randomPostalCode".rand(0, 10000))
            ->setStreet("randomStreet".rand(0, 10000))
            ->setRecruiter($recruiter)
            ->setSource("randomSource".rand(0, 10000))
            ->setCompany("randomCompany".rand(0, 10000))
            ->setCreatedAt()
            ->setUpdatedAt();
        $this->em->persist($employee);
        return $employee;
    }
}