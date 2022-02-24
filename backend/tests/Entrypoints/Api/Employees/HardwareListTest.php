<?php

namespace Tests\Entrypoints\Api\Employees;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Hardware\HardwareAssignment;
use Divante\Bundle\AdventureBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class HardwareListTest extends AbstractEmployeesTest
{
    private ?User $user = null;
    private ?Employee $employee = null;
    private ?HardwareAssignment $hardwareAssignment = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->generateFosUser(['ROLE_MANAGER']);
        $this->employee = $this->generateEmployee($this->user);
        $this->hardwareAssignment = new HardwareAssignment();
        $this->hardwareAssignment->setEmployee($this->employee)
            ->setAssetId(rand(0, 10000))
            ->setSerialNumber("RandomSerialNumber".rand(0, 10000))
            ->setModel("RandomModel".rand(0, 10000))
            ->setManufacturer("RandomManufacturer".rand(0, 10000))
            ->setCategory("RandomCategory".rand(0, 10000));
        $this->em->persist($this->hardwareAssignment);
        $this->em->flush();
    }

    protected function tearDown() : void
    {
        $this->em->remove($this->hardwareAssignment);
        $this->em->remove($this->employee);
        $this->em->remove($this->user);
        $this->em->flush();
        parent::tearDown();
    }

    public function testEntrypoint() : void
    {
        $url = '/api/employees/hardware/'.$this->employee->getId();
        $response = $this->request("GET", $url, $this->user);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        /** @var array $json */
        $json = json_decode($response->getContent(), true);
        $this->assertCount(1, $json);
        $hardware = array_pop($json);
        $this->assertIsArray($hardware);
        $this->assertArrayHasKey('category', $hardware);
        $this->assertEquals($this->hardwareAssignment->getCategory(), $hardware['category']);
        $this->assertArrayHasKey('manufacturer', $hardware);
        $this->assertEquals($this->hardwareAssignment->getManufacturer(), $hardware['manufacturer']);
        $this->assertArrayHasKey('model', $hardware);
        $this->assertEquals($this->hardwareAssignment->getModel(), $hardware['model']);
        $this->assertArrayHasKey('serialNumber', $hardware);
        $this->assertEquals($this->hardwareAssignment->getSerialNumber(), $hardware['serialNumber']);
    }
}
