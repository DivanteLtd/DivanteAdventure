<?php

namespace Divante\Bundle\AdventureBundle\Entity;

use Divante\Bundle\AdventureBundle\Entity\Data\ContractOwner;
use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Divante\Bundle\AdventureBundle\Entity\Data\Timestampable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class PotentialEmployee
 * @package Divante\Bundle\AdventureBundle\Entity
 * @ORM\Table(name="potential_employee")
 * @ORM\Entity(repositoryClass="Divante\Bundle\AdventureBundle\Entity\Repository\PotentialEmployeeRepository")

 */
class PotentialEmployee implements ContractOwner
{
    use Timestampable, Id;

    public const STATUS_POTENTIAL_EMPLOYEE = 0;
    public const STATUS_ACCEPTED = 1;
    public const STATUS_REJECTED = 2;
    public const GENDER_MALE = 0;
    public const GENDER_FEMALE = 1;
    public const REMOTE = 1;
    public const NOT_REMOTE = 2;

    /** @ORM\Column(name="name", type="string", length=150, nullable=false) */
    private string $name;

    /** @ORM\Column(name="lastName", type="string", length=150, nullable=false) */
    private string $lastName;

    /** @ORM\Column(name="email", type="string", length=50, nullable=false) */
    private string $email;

    /** @ORM\Column(name="contract_type", type="string", length=30, nullable=false) */
    private string $contractType;

    /** @ORM\Column(name="welcome_day_date", type="date_immutable", nullable=true) */
    private ?\DateTimeImmutable $welcomeDayDate = null;

    /**
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Tribe", cascade={"remove"})
     * @ORM\JoinColumn(name="designated_tribe_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private ?Tribe $designatedTribe = null;

    /**
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Position")
     * @ORM\JoinColumn(name="designated_position_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private ?Position $designatedPosition;

    /** @ORM\Column(name="designated_hire_date", type="date", nullable=true) */
    private ?\DateTime $designatedHireDate = null;

    /**
     * @ORM\OneToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Employee")
     * @ORM\JoinColumn(name="joined_employee_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private ?Employee $joinedEmployee = null;

    /** @ORM\Column(name="status", type="integer", nullable=false) */
    private int $status = self::STATUS_POTENTIAL_EMPLOYEE;

    /** @ORM\Column(name="rejection_cause", type="string", length=500, nullable=true) */
    private ?string $rejectionCause = null;

    /** @ORM\Column(name="gender", type="integer", nullable=false) */
    private ?int $gender = null;

    /** @ORM\Column(name="date_of_birth", type="datetime", nullable=false) */
    private ?\DateTime $dateOfBirth = null;

    /** @ORM\Column(name="private_phone", type="string", length=50, nullable=false) */
    private ?string $privatePhone = null;

    /** @ORM\Column(name="city", type="string", length=50, nullable=true) */
    private ?string $city = null;

    /** @ORM\Column(name="postal_code", type="string", length=50, nullable=true) */
    private ?string $postalCode = null;

    /** @ORM\Column(name="street", type="string", length=100, nullable=true) */
    private ?string $street = null;

    /** @ORM\Column(name="country", type="string", length=50, nullable=true) */
    private ?string $country = null;

    /** @ORM\Column(name="private_email", type="string", length=50, nullable=false) */
    private ?string $privateEmail = null;

    /**
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Employee")
     * @ORM\JoinColumn(name="recruiter_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private ?Employee $recruiter = null;

    /** @ORM\Column(name="source", type="string", length=50, nullable=true) */
    private ?string $source = null;

    /**
     * Company where employee work previously
     * @ORM\Column(name="company", type="string", length=50, nullable=true)
     */
    private ?string $company = null;
    /** @ORM\Column(name="nip", type="string", nullable=true, length=15) */
    private ?string $nip = null;
    /** @ORM\Column(name="firm_name", type="string", nullable=true, length=255) */
    private ?string $firmName = null;
    /** @ORM\Column(name="firm_address", type="string", nullable=true, length=255) */
    private ?string $firmAddress = null;
    /** @ORM\Column(name="language", type="string", nullable=false, length=2) */
    private string $language = 'en';
    /** @ORM\Column(name="work", type="boolean") */
    private bool $work = false;
    /**
     *  @ORM\Column(name="outsource_sub_type", type="string", nullable=true)
     */
    private ?string $outsourceSubType;

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

    public function getEmail() : string
    {
        return $this->email;
    }

    public function setEmail(string $email) : self
    {
        $this->email = $email;
        return $this;
    }

    public function getContractType() : string
    {
        return $this->contractType;
    }

    public function setContractType(string $contractType) : self
    {
        $this->contractType = $contractType;
        return $this;
    }

    public function getDesignatedTribe() : ?Tribe
    {
        return $this->designatedTribe;
    }

    public function setDesignatedTribe(?Tribe $tribe) : self
    {
        $this->designatedTribe = $tribe;
        return $this;
    }

    public function getDesignatedPosition() : ?Position
    {
        return $this->designatedPosition;
    }

    public function setDesignatedPosition(?Position $position) : self
    {
        $this->designatedPosition = $position;
        return $this;
    }

    public function getDesignatedHireDate() : ?\DateTime
    {
        return $this->designatedHireDate;
    }

    public function setDesignatedHireDate(?\DateTime $hireDate) : self
    {
        $this->designatedHireDate = $hireDate;
        return $this;
    }

    public function getJoinedEmployee() : ?Employee
    {
        return $this->joinedEmployee;
    }

    public function setJoinedEmployee(?Employee $employee) : self
    {
        $this->joinedEmployee = $employee;
        return $this;
    }

    public function getStatus() : int
    {
        return $this->status;
    }

    public function setStatus(int $status) : self
    {
        $this->status = $status;
        return $this;
    }

    public function getRejectionCause() : ?string
    {
        return $this->rejectionCause;
    }

    public function setRejectionCause(?string $cause) : self
    {
        $this->rejectionCause = $cause;
        return $this;
    }

    public function getGender(): ?int
    {
        return $this->gender;
    }

    public function setGender(int $gender): self
    {
        $this->gender = $gender;
        return $this;
    }

    public function getDateOfBirth() : ?\DateTime
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(\DateTime $dateOfBirth) : self
    {
        $this->dateOfBirth = $dateOfBirth;
        return $this;
    }

    public function getPrivatePhone() : ?string
    {
        return $this->privatePhone;
    }

    public function setPrivatePhone(?string $privatePhone) : self
    {
        $this->privatePhone = $privatePhone;
        return $this;
    }

    public function getCity() : ?string
    {
        return $this->city;
    }

    public function setCity(?string $city) : self
    {
        $this->city = $city;
        return $this;
    }

    public function getPostalCode() : ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode) : self
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    public function getStreet() : ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street) : self
    {
        $this->street = $street;
        return $this;
    }

    public function getCountry() : ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country) : self
    {
        $this->country = $country;
        return $this;
    }

    public function getPrivateEmail() : ?string
    {
        return $this->privateEmail;
    }

    public function setPrivateEmail(?string $privateEmail) : self
    {
        $this->privateEmail = $privateEmail;
        return $this;
    }

    public function getRecruiter() : ?Employee
    {
        return $this->recruiter;
    }

    public function setRecruiter(?Employee $recruiter) : self
    {
        $this->recruiter = $recruiter;
        return $this;
    }

    public function getSource() : ?string
    {
        return $this->source;
    }

    public function setSource(?string $source) : self
    {
        $this->source = $source;
        return $this;
    }

    public function getCompany() : ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company) : self
    {
        $this->company = $company;
        return $this;
    }
    public function getNip(): ?string
    {
        return $this->nip;
    }

    public function setNip(?string $nip): self
    {
        $this->nip = $nip;
        return $this;
    }

    public function getFirmName(): ?string
    {
        return $this->firmName;
    }

    public function setFirmName(?string $firmName): self
    {
        $this->firmName = $firmName;
        return $this;
    }

    public function getWelcomeDayDate(): ?\DateTimeImmutable
    {
        return $this->welcomeDayDate;
    }

    public function setWelcomeDayDate(?\DateTimeImmutable $welcomeDayDate): self
    {
        $this->welcomeDayDate = $welcomeDayDate;
        return $this;
    }

    public function getFirmAddress(): ?string
    {
        return $this->firmAddress;
    }

    public function setFirmAddress(?string $firmAddress): self
    {
        $this->firmAddress = $firmAddress;
        return $this;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function setLanguage(string $language): self
    {
        $this->language = $language;
        return $this;
    }

    public function isWork(): bool
    {
        return $this->work;
    }

    public function setWork(bool $work): self
    {
        $this->work = $work;
        return $this;
    }

    public function getOutsourceSubType(): ?string
    {
        return $this->outsourceSubType;
    }

    public function setOutsourceSubType(?string $outsourceSubType): self
    {
        $this->outsourceSubType = $outsourceSubType;
        return $this;
    }
}
