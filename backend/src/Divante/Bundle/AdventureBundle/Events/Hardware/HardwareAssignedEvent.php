<?php

namespace Divante\Bundle\AdventureBundle\Events\Hardware;

use \Symfony\Component\EventDispatcher\Event;

class HardwareAssignedEvent extends Event
{
    private string $firstName;
    private string $lastName;
    private string $manufacturer;
    private string $model;
    private string $serialNumber;
    private string $updatedAt;

    public function __construct(
        string $firstName,
        string $lastName,
        string $manufacturer,
        string $model,
        string $serialNumber,
        string $updatedAt
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->model = $model;
        $this->manufacturer = $manufacturer;
        $this->serialNumber = $serialNumber;
        $this->updatedAt = $updatedAt;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getManufacturer(): string
    {
        return $this->manufacturer;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getSerialNumber(): string
    {
        return $this->serialNumber;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }
}
