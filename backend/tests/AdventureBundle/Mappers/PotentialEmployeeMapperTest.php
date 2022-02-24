<?php

namespace Tests\AdventureBundle\Mappers;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\PotentialEmployee;
use Divante\Bundle\AdventureBundle\Mappers\Employee\HrEmployeeMapper;
use Divante\Bundle\AdventureBundle\Mappers\PositionMapper;
use Divante\Bundle\AdventureBundle\Mappers\PotentialEmployeeMapper;
use Divante\Bundle\AdventureBundle\Mappers\TribeMapper;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\FoundationTestCase;
use DateTime;

class PotentialEmployeeMapperTest extends FoundationTestCase
{
    public function testMapper() : void
    {
        $id = rand(0, 10000);
        $date = (new \DateTime())->getTimestamp();
        $name = "RandomName".rand(0, 10000);
        $lastName = "RandomLastName".rand(0, 10000);
        $contractType = "RandomLastName".rand(0, 10000);
        $rejectionCause = "RandomLastName".rand(0, 10000);
        $gender = rand(0, 10000);
        $dateOfBirth = new \DateTime();
        $email = "RandomEmail".rand(0, 10000)."@email.tld";
        $hireDate = (new \DateTime())->setTimestamp(rand(0, 1000000));
        $position = null;
        $tribe = null;
        $joinedEmployee = null;
        $privatePhone = rand(0, 10000);
        $privateEmail = "randomPrivateEmail".rand(0, 10000)."@example.com";
        $recruiter = $this->prepareRandomEmployee();
        $city = "randomCity".rand(0, 10000);
        $postalCode = "randomPostalCode".rand(0, 10000);
        $street = "randomStreet".rand(0, 10000);
        $country = "randomCountry".rand(0, 10000);
        $source = "randomSource".rand(0, 10000);
        $company = "randomCompany".rand(0, 10000);
        $nip = '5312456586';
        $firmName = 'Random firm name';
        $firmAddress = 'Random firm address';


        $employee = new PotentialEmployee();
        $this->setId($employee, $id);
        $employee
            ->setName($name)
            ->setLastName($lastName)
            ->setContractType($contractType)
            ->setRejectionCause($rejectionCause)
            ->setDesignatedHireDate($hireDate)
            ->setDesignatedPosition($position)
            ->setDesignatedTribe($tribe)
            ->setJoinedEmployee($joinedEmployee)
            ->setGender($gender)
            ->setDateOfBirth($dateOfBirth)
            ->setEmail($email)
            ->setDateOfBirth(new DateTime())
            ->setPrivatePhone($privatePhone)
            ->setPrivateEmail($privateEmail)
            ->setCity($city)
            ->setCountry($country)
            ->setPostalCode($postalCode)
            ->setStreet($street)
            ->setRecruiter($recruiter)
            ->setSource($source)
            ->setCompany($company)
            ->setCreatedAt()
            ->setUpdatedAt()
            ->setNip($nip)
            ->setFirmName($firmName)
            ->setFirmAddress($firmAddress);

        $mapped = $this->getMapper()->mapEntity($employee);

        $this->assertArrayHasKey('createdAt', $mapped);
        $this->assertEqualsWithDelta($date, $mapped['createdAt'], 5);
        $this->assertArrayHasKey('updatedAt', $mapped);
        $this->assertEqualsWithDelta($date, $mapped['updatedAt'], 5);
        $this->assertArrayHasKey('id', $mapped);
        $this->assertEquals($id, $mapped['id']);
        $this->assertArrayHasKey('name', $mapped);
        $this->assertEquals($name, $mapped['name']);
        $this->assertArrayHasKey('lastName', $mapped);
        $this->assertEquals($lastName, $mapped['lastName']);
        $this->assertArrayHasKey('email', $mapped);
        $this->assertEquals($email, $mapped['email']);
        $this->assertArrayHasKey('contractType', $mapped);
        $this->assertEquals($contractType, $mapped['contractType']);
        $this->assertArrayHasKey('gender', $mapped);
        $this->assertEquals($gender, $mapped['gender']);
        $this->assertArrayHasKey('dateOfBirth', $mapped);
        $this->assertEquals($dateOfBirth->format('Y-m-d'), $mapped['dateOfBirth']);
        $this->assertArrayHasKey('privatePhone', $mapped);
        $this->assertEquals($privatePhone, $mapped['privatePhone']);
        $this->assertArrayHasKey('privateEmail', $mapped);
        $this->assertEquals($privateEmail, $mapped['privateEmail']);
        $this->assertArrayHasKey('source', $mapped);
        $this->assertEquals($source, $mapped['source']);
        $this->assertArrayHasKey('street', $mapped);
        $this->assertEquals($street, $mapped['street']);
        $this->assertArrayHasKey('postalCode', $mapped);
        $this->assertEquals($postalCode, $mapped['postalCode']);
        $this->assertArrayHasKey('city', $mapped);
        $this->assertEquals($city, $mapped['city']);
        $this->assertArrayHasKey('country', $mapped);
        $this->assertEquals($country, $mapped['country']);
        $this->assertArrayHasKey('company', $mapped);
        $this->assertEquals($company, $mapped['company']);
        $this->assertArrayHasKey('recruiter', $mapped);
        $this->assertEquals($recruiter->getName(), $mapped['recruiter']['name']);
        $this->assertArrayHasKey('nip', $mapped);
        $this->assertArrayHasKey('firmName', $mapped);
        $this->assertArrayHasKey('firmAddress', $mapped);
        $this->assertEquals($nip, $mapped['nip']);
        $this->assertEquals($firmName, $mapped['firmName']);
        $this->assertEquals($firmAddress, $mapped['firmAddress']);
    }

    private function prepareRandomEmployee() : Employee
    {
        $employee = new Employee();
        $this->setId($employee, rand(1, 10000));
        $employee->setName("RandomName".rand(0, 10000))
            ->setLastName("RandomLastName".rand(0, 10000))
            ->setPhoto("RandomPhoto".rand(0, 10000));
        return $employee;
    }

    public function testMapperWithHireDate() : void
    {
        $id = rand(0, 10000);
        $gender = rand(0, 10000);
        $date = (new \DateTime())->getTimestamp();
        $dateOfBirth = new \DateTime();
        $hireDate = (new \DateTime())->setTimestamp(rand(0, 1000000));
        $name = "RandomName".rand(0, 10000);
        $lastName = "RandomLastName".rand(0, 10000);
        $contractType = "RandomLastName".rand(0, 10000);
        $email = "RandomEmail".rand(0, 10000)."@email.tld";
        $rejectionCause = "RandomLastName".rand(0, 10000);
        $position = null;
        $tribe = null;
        $joinedEmployee = null;

        $employee = new PotentialEmployee();
        $this->setId($employee, $id);
        $employee
            ->setCreatedAt()
            ->setUpdatedAt()
            ->setName($name)
            ->setLastName($lastName)
            ->setContractType($contractType)
            ->setDesignatedHireDate($hireDate)
            ->setRejectionCause($rejectionCause)
            ->setDesignatedPosition($position)
            ->setDesignatedTribe($tribe)
            ->setJoinedEmployee($joinedEmployee)
            ->setGender($gender)
            ->setDateOfBirth($dateOfBirth)
            ->setEmail($email);

        $mapped = $this->getMapper($employee)->mapEntity($employee);

        $this->assertArrayHasKey('createdAt', $mapped);
        $this->assertEqualsWithDelta($date, $mapped['createdAt'], 5);
        $this->assertArrayHasKey('updatedAt', $mapped);
        $this->assertEqualsWithDelta($date, $mapped['updatedAt'], 5);
        $this->assertArrayHasKey('id', $mapped);
        $this->assertEquals($id, $mapped['id']);
        $this->assertArrayHasKey('name', $mapped);
        $this->assertEquals($name, $mapped['name']);
        $this->assertArrayHasKey('lastName', $mapped);
        $this->assertEquals($lastName, $mapped['lastName']);
        $this->assertArrayHasKey('email', $mapped);
        $this->assertEquals($email, $mapped['email']);
        $this->assertArrayHasKey('contractType', $mapped);
        $this->assertEquals($contractType, $mapped['contractType']);
        $this->assertArrayHasKey('hireDate', $mapped);
        $this->assertEquals($hireDate->format('Y-m-d'), $mapped['hireDate']);
        $this->assertArrayHasKey('gender', $mapped);
        $this->assertEquals($gender, $mapped['gender']);
        $this->assertArrayHasKey('dateOfBirth', $mapped);
        $this->assertEquals($dateOfBirth->format('Y-m-d'), $mapped['dateOfBirth']);
    }


    private function getMapper( ) : PotentialEmployeeMapper
    {
        $positionMapper = new PositionMapper();
        $tribeMapper = new TribeMapper();
        /** @var HrEmployeeMapper|MockObject $employeeMapper */
        $employeeMapper = $this->getMockBuilder(HrEmployeeMapper::class)
            ->disableOriginalConstructor()
            ->setMethods(['mapEmployeeToJson'])
            ->getMock();
        $employeeMapper->expects($this->any())->method('mapEmployeeToJson')->willReturnCallback(
            function (Employee $entity) {
                return [ 'name' => $entity->getName() ];
            }
        );
        return new PotentialEmployeeMapper($positionMapper, $tribeMapper, $employeeMapper);
    }
}
