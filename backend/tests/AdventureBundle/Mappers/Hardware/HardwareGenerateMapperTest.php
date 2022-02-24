<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Tests\AdventureBundle\Mappers\Hardware;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Hardware\HardwareAgreement;
use Divante\Bundle\AdventureBundle\Entity\Hardware\HardwareAssignment;
use Divante\Bundle\AdventureBundle\Mappers\Hardware\HardwareGenerateMapper;
use Tests\FoundationTestCase;

class HardwareGenerateMapperTest extends FoundationTestCase
{
    private HardwareGenerateMapper $mapper;

    protected function setUp() : void
    {
        $this->mapper = new HardwareGenerateMapper();
    }

    public function testIdPassed() : void
    {
        $hardwareAgreement = $this->prepareRandomHardwareAgreement();
        $mapper = $this->mapper;
        $result = $mapper->mapEntity($hardwareAgreement);
        $this->assertEquals($hardwareAgreement->getId(), $result['id']);
    }

    public function testNamePassed() : void
    {
        $hardwareAgreement = $this->prepareRandomHardwareAgreement();
        $mapper = $this->mapper;
        $result = $mapper->mapEntity($hardwareAgreement);
        $this->assertEquals($hardwareAgreement->getAssignment()->getEmployee()->getName(), $result['name']);
    }

    public function testLastNamePassed() : void
    {
        $hardwareAgreement = $this->prepareRandomHardwareAgreement();
        $mapper = $this->mapper;
        $result = $mapper->mapEntity($hardwareAgreement);
        $this->assertEquals($hardwareAgreement->getAssignment()->getEmployee()->getLastName(), $result['lastName']);
    }

    public function testContractPassed() : void
    {
        $hardwareAgreement = $this->prepareRandomHardwareAgreement();
        $mapper = $this->mapper;
        $result = $mapper->mapEntity($hardwareAgreement);
        $this->assertEquals(
            $hardwareAgreement->getAssignment()->getEmployee()->getContractType(),
            $result['contract']
        );
    }

    public function testCategoryPassed() : void
    {
        $hardwareAgreement = $this->prepareRandomHardwareAgreement();
        $mapper = $this->mapper;
        $result = $mapper->mapEntity($hardwareAgreement);
        $this->assertEquals($hardwareAgreement->getAssignment()->getCategory(), $result['category']);
    }

    public function testManufacturerPassed() : void
    {
        $hardwareAgreement = $this->prepareRandomHardwareAgreement();
        $mapper = $this->mapper;
        $result = $mapper->mapEntity($hardwareAgreement);
        $this->assertEquals($hardwareAgreement->getAssignment()->getManufacturer(), $result['manufacturer']);
    }

    public function testModelPassed() : void
    {
        $hardwareAgreement = $this->prepareRandomHardwareAgreement();
        $mapper = $this->mapper;
        $result = $mapper->mapEntity($hardwareAgreement);
        $this->assertEquals($hardwareAgreement->getAssignment()->getModel(), $result['model']);
    }

    public function testSerialNumberPassed() : void
    {
        $hardwareAgreement = $this->prepareRandomHardwareAgreement();
        $mapper = $this->mapper;
        $result = $mapper->mapEntity($hardwareAgreement);
        $this->assertEquals($hardwareAgreement->getAssignment()->getSerialNumber(), $result['serialNumber']);
    }

    public function testGeneratedPassed() : void
    {
        $hardwareAgreement = $this->prepareRandomHardwareAgreement();
        $mapper = $this->mapper;
        $result = $mapper->mapEntity($hardwareAgreement);
        $this->assertEquals($hardwareAgreement->getGenerationDate()->format('Y-m-d'), $result['generated']);
    }

    private function prepareRandomHardwareAgreement() : HardwareAgreement
    {
        $name = "name".rand(0, 10000);
        $lastName = "lastName".rand(0, 10000);
        $category = "category".rand(0, 10000);
        $manufacturer = "manufacturer".rand(0, 10000);
        $model = "model".rand(0, 10000);
        $serialNumber = "serialNumber".rand(0, 10000);
        $generated = new \DateTime();

        $user = new Employee();
        $user
            ->setContractId(rand(1, 3))
            ->setName($name)
            ->setLastName($lastName);

        $hardwareAssignment = new HardwareAssignment();
        $hardwareAssignment
            ->setEmployee($user)
            ->setCategory($category)
            ->setManufacturer($manufacturer)
            ->setModel($model)
            ->setSerialNumber($serialNumber);

        $hardwareAgreement = new HardwareAgreement();
        $this->setId($hardwareAgreement, rand(1, 10000));

        return $hardwareAgreement
            ->setAssignment($hardwareAssignment)
            ->setGenerationDate($generated)
            ->setCreatedAt()
            ->setUpdatedAt();
    }
}
