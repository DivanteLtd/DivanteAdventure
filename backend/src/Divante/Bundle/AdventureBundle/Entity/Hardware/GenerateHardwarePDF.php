<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\Entity\Hardware;

use Divante\Bundle\AdventureBundle\Entity\Data\Id;

class GenerateHardwarePDF
{
    use Id;

    /** @ORM\Column(name="name", type="string", length=150, nullable=false) */
    private string $name;

    /** @ORM\Column(name="last_name", type="string", length=150, nullable=false) */
    private string $lastName;

    /** @ORM\Column(name="contract", type="string", length=10, nullable=false) */
    private string $contract;

    /** @ORM\Column(name="category", type="string", length=100, nullable=true) */
    private ?string $category;

    /** @ORM\Column(name="manufacturer", type="string", length=100, nullable=true) */
    private ?string $manufacturer;

    /** @ORM\Column(name="model", type="string", length=100, nullable=true) */
    private ?string $model;

    /** @ORM\Column(name="serial_number", type="string", length=100, nullable=true) */
    private ?string $serialNumber;

    private ?string $PESEL;

    public function getPESEL(): ?string
    {
        return $this->PESEL;
    }

    private ?string $NIP;
    private ?string $company;
    private ?string $headquarters;

    public function getName() : string
    {
        return $this->name;
    }

    public function setName(string $name) : self
    {
        $this->name = $name;
        return $this;
    }

    public function getLastName() : string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName) : self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getContract() : string
    {
        return $this->contract;
    }

    public function setContract(string $contract) : self
    {
        $this->contract = $contract;
        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category) : self
    {
        $this->category = $category;
        return $this;
    }

    public function getManufacturer() : ?string
    {
        return $this->manufacturer;
    }

    public function setManufacturer(?string $manufacturer) : self
    {
        $this->manufacturer = $manufacturer;
        return $this;
    }

    public function getModel() : ?string
    {
        return $this->model;
    }

    public function setModel(?string $model) : self
    {
        $this->model = $model;
        return $this;
    }

    public function getSerialNumber() : ?string
    {
        return $this->serialNumber;
    }

    public function setSerialNumber(?string $serialNumber) : self
    {
        $this->serialNumber = $serialNumber;
        return $this;
    }


    public function setPESEL(?string $PESEL): self
    {
        $this->PESEL = $PESEL;
        return $this;
    }

    public function getNIP(): ?string
    {
        return $this->NIP;
    }

    public function setNIP(?string $NIP): self
    {
        $this->NIP = $NIP;
        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): self
    {
        $this->company = $company;
        return $this;
    }

    public function getHeadquarters(): ?string
    {
        return $this->headquarters;
    }

    public function setHeadquarters(?string $headquarters): self
    {
        $this->headquarters = $headquarters;
        return $this;
    }
}
