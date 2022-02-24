<?php

namespace Divante\Bundle\AdventureBundle\Entity\Hardware;

use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Doctrine\ORM\Mapping as ORM;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\PotentialEmployee;

/**
 * HardwareAssignment
 *
 * @ORM\Table(name="employee_hardware")
 * @ORM\Entity(repositoryClass="Divante\Bundle\AdventureBundle\Entity\Repository\Hardware\HardwareAssignmentRepository")
 */
class HardwareAssignment
{
    use Id;

    /**
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Employee")
     * @ORM\JoinColumn(name="employee_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private ?Employee $employee = null;

    /**
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\PotentialEmployee")
     * @ORM\JoinColumn(name="potential_employee_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private ?PotentialEmployee $potentialEmployee = null;

    /** @ORM\Column(name="asset_id", type="integer", nullable=false) */
    private int $assetId;

    /** @ORM\Column(name="category", type="string", length=100, nullable=true) */
    private ?string $category = null;

    /** @ORM\Column(name="manufacturer", type="string", length=100, nullable=true) */
    private ?string $manufacturer = null;

    /** @ORM\Column(name="model", type="string", length=100, nullable=true) */
    private ?string $model = null;

    /** @ORM\Column(name="serialNumber", type="string", length=100, nullable=true) */
    private ?string $serialNumber = null;

    /** @ORM\Column(name="send_email", type="boolean", options={"default" : false}) */
    private bool $sendEmail = false;

    public function getEmployee() : ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee) : self
    {
        $this->employee = $employee;
        return $this;
    }

    public function getPotentialEmployee() : ?PotentialEmployee
    {
        return $this->potentialEmployee;
    }

    public function setPotentialEmployee(?PotentialEmployee $potentialEmployee) : self
    {
        $this->potentialEmployee = $potentialEmployee;
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

    public function getAssetId(): int
    {
        return $this->assetId;
    }

    public function setAssetId(int $assetId): self
    {
        $this->assetId = $assetId;
        return $this;
    }

    public function isSendEmail(): bool
    {
        return $this->sendEmail;
    }

    public function setSendEmail(bool $sendEmail): self
    {
        $this->sendEmail = $sendEmail;
        return $this;
    }
}
