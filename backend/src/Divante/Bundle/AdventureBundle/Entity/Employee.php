<?php

namespace Divante\Bundle\AdventureBundle\Entity;

use DateTime;
use Divante\Bundle\AdventureBundle\Annotation\Exporter;
use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Divante\Bundle\AdventureBundle\Entity\Data\SchedulerHideable;
use Divante\Bundle\AdventureBundle\Entity\Data\SlackEntity;
use Divante\Bundle\AdventureBundle\Entity\Data\Timestampable;
use Divante\Bundle\AdventureBundle\Entity\FAQ\FAQCategory;
use Divante\Bundle\AdventureBundle\Services\Slack\Api\SlackReceiver;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Validator\Constraints as Assert;
use Divante\Bundle\AdventureBundle\Entity\Data\ContractOwner;

/**
 * Employee
 *
 * @ORM\Table(name="employee")
 * @ORM\Entity(repositoryClass="Divante\Bundle\AdventureBundle\Entity\Repository\EmployeeRepository")
 */
class Employee implements SchedulerHideable, SlackReceiver, ContractOwner
{
    use Timestampable, Id, SlackEntity;

    public const GENDER_MALE = 0;
    public const GENDER_FEMALE = 1;

    public const CONTRACT_B2B_LUMP_SUM = 1; // Business to business lump sum
    public const CONTRACT_B2B_HOURLY = 2; // Business to business hourly billing
    public const CONTRACT_CLC_LUMP_SUM = 3; // Civil law contract lump sum
    public const CONTRACT_CLC_HOURLY = 4; // Civil law contract hourly billing
    public const CONTRACT_COE = 5; // Contract of employment
    public const CONTRACT_OUTSOURCE = 6; // Outsourcing

    public const MAX_VERIFICATION_TIME_SECONDS = 300; // 5 minutes
    public const MAX_FAIL_COUNT = 3; // on third fail account is locked

    public const WORK_FROM_OFFICE = 1; // employee declared working mode - office
    public const WORK_REMOTELY = 2; // employee declared working mode - remotely
    public const WORK_PARTIAL_REMOTELY = 3; // employee declared working mode - partial remotely

    /**
     * @ORM\OneToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\EmployeeEndCooperation", mappedBy="employee")
     */
    private ?EmployeeEndCooperation $endingCooperation = null;
    /** @ORM\OneToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\User", mappedBy="employee") */
    private ?User $fosUser = null;

    /**
     * @Assert\NotBlank @Assert\Email
     * @ORM\Column(name="email", type="string", length=254, nullable=false)
     * @Exporter(humanName="Email", export=true, columnName="email")
     */
    private string $email;

    /**
     * @Assert\NotBlank @Assert\Length(min=2, max=150)
     * @ORM\Column(name="name", type="string", length=150, nullable=false)
     * @Exporter(humanName="First Name", export=true, columnName="name")
     */
    private string $name;

    /**
     * @var string
     * @Assert\NotBlank @Assert\Length(min=2, max=150)
     * @ORM\Column(name="last_name", type="string", length=150, nullable=false)
     * @Exporter(humanName="Last Name", export=true, columnName="last_name")
     */
    private string $lastName;

    /** @ORM\Column(name="photo", type="string", length=255, nullable=true)
     * @Exporter(humanName="Photo", export=false, columnName="photo")
     */
    private ?string $photo = null;
    /** @ORM\Column(name="phone", type="string", length=50, nullable=true)
     * @Exporter(humanName="Company Phone", export=true, columnName="phone")
     */
    private ?string $phone = null;
    /** @ORM\Column(name="private_phone", type="string", length=50, nullable=true)
     * @Exporter(humanName="Private Phone", export=true, columnName="private_phone")
     */
    private ?string $privatePhone = null;
    /** @ORM\Column(name="car", type="string", length=50, nullable=true)
     * @Exporter(humanName="Car Plate", export=true, columnName="car")
     */
    private ?string $car = null;
    /** @ORM\Column(name="city", type="string", length=50, nullable=true)
     * @Exporter(humanName="City", export=true, columnName="city")
     */
    private ?string $city = null;
    /** @ORM\Column(name="postal_code", type="string", length=50, nullable=true)
     * @Exporter(humanName="Postal Code", export=true, columnName="postal_code")
     */
    private ?string $postalCode = null;
    /** @ORM\Column(name="street", type="string", length=100, nullable=true)
     * @Exporter(humanName="Street", export=true, columnName="street")
     */
    private ?string $street = null;
    /** @ORM\Column(name="country", type="string", length=100, nullable=true)
     * @Exporter(humanName="Country", export=true, columnName="country")
     */
    private ?string $country = null;
    /** @ORM\Column(name="work_mode", type="integer", nullable=true) */
    private ?int $workMode = 0;
    /** @ORM\Column(name="hired_at", type="date", nullable=true)
     * @Exporter (humanName="Hired At", export=true, columnName="hired_at")
     */
    private ?DateTime $hiredAt = null;
    /** @ORM\Column(name="hired_to", type="date", nullable=true)
     * @Exporter(humanName="Hired To", export=true, columnName="hired_to")
     */
    private ?DateTime $hiredTo = null;
    /** @ORM\Column(name="gender", type="integer", nullable=true)
     * @Exporter(humanName="Gender", export=true, columnName="gender")
     */
    private ?int $gender = null;
    /** @ORM\Column(name="emergency_first_name", type="string", nullable=true)
     * @Exporter(humanName="Emergency First Name", export=true, columnName="emergency_first_name")
     */
    private ?string $emergencyFirstName = null;
    /** @ORM\Column(name="emergency_last_name", type="string", nullable=true)
     * @Exporter(humanName="Emergency Last Name", export=true, columnName="emergency_last_name")
     */
    private ?string $emergencyLastName = null;
    /** @ORM\Column(name="emergency_address", type="string", nullable=true)
     * @Exporter(humanName="Emergency Address", export=true, columnName="emergency_last_name")
     */
    private ?string $emergencyAddress = null;
    /** @ORM\Column(name="emergency_phone", type="string", nullable=true)
     * @Exporter(humanName="Emergency Phone", export=true, columnName="emergency_phone")
     */
    private ?string $emergencyPhone = null;
    /** @ORM\Column(name="pin", type="string", nullable=true, length=255) */
    private ?string $pin = null;
    /** @ORM\Column(name="pin_locked", type="boolean") */
    private bool $pinLocked = false;
    /** @ORM\Column(name="first_fail_time", type="datetime", nullable=true) */
    private ?DateTime $firstFailTime = null;
    /** @ORM\Column(name="fail_count", type="integer") */
    private int $failCount = 0;
    /** @ORM\Column(name="date_of_birth", type="date", nullable=true)
     * @Exporter(humanName="Birthday", export=true, columnName="date_of_birth")
     */
    private ?DateTime $dateOfBirth = null;
    /** @ORM\Column(name="child_care", type="integer")
     * @Exporter(humanName="Has Child", export=true, columnName="child_care")
     */
    private int $childCare = 0;
    /** @ORM\Column(name="gitlab_id", type="integer", nullable=true) */
    private ?int $gitlabId = null;
    /** @ORM\Column(name="language", type="string", length=2, nullable=true) */
    private ?string $language = null;
    /** @ORM\Column(name="avaza_id", type="string", length=16, nullable=true) */
    private ?string $avazaId = null;
    /** @ORM\Column(name="nip", type="string", nullable=true, length=15)
     * @Exporter(humanName="Firm NIP", export=true, columnName="nip")
     */
    private ?string $nip = null;
    /** @ORM\Column(name="firm_name", type="string", nullable=true, length=255)
     * @Exporter(humanName="Firm Name", export=true, columnName="firm_name")
     */
    private ?string $firmName = null;
    /** @ORM\Column(name="firm_address", type="string", nullable=true, length=255)
     * @Exporter(humanName="Firm Address", export=true, columnName="firm_address")
     */
    private ?string $firmAddress = null;
    /** @ORM\Column(name="contract_id", type="integer", nullable=true)
     * @Exporter(
     *     humanName="Contract Name",
     *      export=true,
     *      columnName="contract_id",
     *      decoratorClass="Divante\Bundle\AdventureBundle\Services\Decorators\Contract"
     * )
     */
    private ?int $contract = null;
    /** @ORM\Column(name="data_update", type="datetime", nullable=true) */
    private ?DateTime $dataUpdate = null;
    /** @ORM\Column(name="tech_tribe_leader", type="boolean")
     * @Exporter(humanName="Is Tech Tribe", export=true, columnName="tech_tribe_leader")
     */
    private bool $techTribeLeader = false;
    /**
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Tribe", inversedBy="employees")
     * @ORM\JoinColumn(name="tribe_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     * @Exporter(
     *     humanName="Tribe name",
     *      export=true,
     *      decoratorClass="Divante\Bundle\AdventureBundle\Services\Decorators\Tribe",
     *     columnName="tribe_id"
     * )
     */
    private ?Tribe $tribe = null;

    /**
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Position", inversedBy="employees")
     * @ORM\JoinColumn(name="position_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     * @Exporter(
     *     humanName="Position Name",
     *      export=true,
     *      decoratorClass="Divante\Bundle\AdventureBundle\Services\Decorators\Position",
     *      columnName="position_id"
     * )
     *
     */
    private ?Position $position = null;

    /**
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Level", inversedBy="employees")
     * @ORM\JoinColumn(name="level_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     * @Exporter(
     *     humanName="Level Name",
     *      export=true,
     *      decoratorClass="Divante\Bundle\AdventureBundle\Services\Decorators\Level",
     *      columnName="level_id"
     * )
     */
    private ?Level $level = null;

    /**
     * @var Collection<int,EmployeeSkillArea>
     * @ORM\OneToMany(targetEntity="Divante\Bundle\AdventureBundle\Entity\EmployeeSkillArea", mappedBy="employee")
     */
    private Collection $employeeSkillAreas;

    /**
     * @var Collection<int,LeavePeriod>
     * @ORM\OneToMany(targetEntity="Divante\Bundle\AdventureBundle\Entity\LeavePeriod", mappedBy="employee")
     */
    private Collection $periods;

    /** @ORM\Column(name="job_time_value", type="decimal", nullable=false) */
    private float $jobTimeValue = 0;

    /**
     * @var Collection<int,Evidence>
     * @ORM\OneToMany(targetEntity="Divante\Bundle\AdventureBundle\Entity\Evidence", mappedBy="employee")
     */
    private Collection $employeeEvidences;

    /**
     * @var Collection<int,Tribe>
     * @ORM\ManyToMany(targetEntity="Divante\Bundle\AdventureBundle\Entity\Tribe", mappedBy="responsible")
     */
    private Collection $tribeResponsible;

    /**
     * @var Collection<int,Evidence>
     * @ORM\OneToMany(targetEntity="Divante\Bundle\AdventureBundle\Entity\Evidence", mappedBy="overtimeManager")
     */
    private Collection $evidenceRequests;

    /**
     * @var Collection<int,EmployeeAgreement>
     * @ORM\OneToMany(targetEntity="Divante\Bundle\AdventureBundle\Entity\EmployeeAgreement", mappedBy="employee")
     */
    private Collection $agreements;

    /**
     * @var Collection<int,FAQCategory>
     * @ORM\ManyToMany(targetEntity="Divante\Bundle\AdventureBundle\Entity\FAQ\FAQCategory", mappedBy="employee")
     */
    private Collection $fAQCategory;

    /**
     * @var Collection<int,Employee>
     * @ORM\ManyToMany(targetEntity="Divante\Bundle\AdventureBundle\Entity\Employee", inversedBy="leaderStructures")
     * @ORM\JoinTable(name="leaders",
     *     joinColumns={@ORM\JoinColumn(name="employee_id", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="leader_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    private Collection $leaders;

    /**
     * @ORM\ManyToMany(targetEntity="Divante\Bundle\AdventureBundle\Entity\Employee", mappedBy="leaders")
     */
    private Collection $leaderStructures;

    /** @ORM\Column(name="employee_code", type="string", nullable=true, length=100)
     * @Exporter(humanName="Employee code", export=true, columnName="employee_code")
     */
    private ?string $employeeCode = null;

    /**
     * @Assert\NotBlank @Assert\Length(min=2, max=2)
     * @ORM\Column(name="company_branch", type="string", length=2, nullable=false, options={"default":"PL"})
     */
    private string $companyBranch = 'PL';

    /**
     * @var Collection<int,Contract>
     * @ORM\OneToMany(targetEntity="Divante\Bundle\AdventureBundle\Entity\Contract", mappedBy="employee")
     */
    private Collection $contracts;

    /**
     * @ORM\OneToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Employee")
     * @ORM\JoinColumn(name="superior_id", referencedColumnName="id")
     */
    private ?Employee $superior;

    /** @ORM\Column(name="finance_code", type="string", nullable=true, length=255)
     * @Exporter(humanName="Finance Code", export=true, columnName="finance_code")
     */
    private ?string $financeCode;
    /**
     * @Assert\NotBlank
     * @ORM\Column(name="token_expiration_seconds", type="integer", nullable=false, options={"default":86400})
     */
    private int $tokenExpirationSeconds = 86400;

    /** @ORM\Column(name="student", type="boolean", nullable=true)
     * @Exporter(humanName="Is Student", export=true, columnName="student")
     */
    private ?bool $student = false;

    /**
     * @ORM\Column(name="tax_deductible_costs", type="integer", nullable=true,  options={"default":0})
     */
    private ?int $taxDeductibleCosts;

    /**
     * @ORM\Column(name="work_street", type="string", nullable=true )
     * @Exporter(humanName="Work Street", export=true, columnName="work_street")
     */
    private ?string $workStreet;
    /**
     * @ORM\Column(name="work_city", type="string", nullable=true )
     * @Exporter(humanName="Work City", export=true, columnName="work_city")
     */
    private ?string $workCity;
    /**
     * @ORM\Column(name="work_postal_code", type="string", nullable=true )
     * @Exporter(humanName="Work Postal Code", export=true, columnName="work_postal_code")
     */
    private ?string $workPostalCode;
    /**
     * @ORM\Column(name="work_country", type="string", nullable=true )
     * @Exporter(humanName="Work Country", export=true, columnName="work_country")
     */
    private ?string $workCountry;
    /**
     * @ORM\Column(name="shoe_size", type="string", nullable=true )
     * @Exporter(humanName="Shoe size", export=true, columnName="shoe_size")
     */
    private ?string $shoeSize;
    /**
     * @ORM\Column(name="sweatshirt_size", type="string", nullable=true )
     * @Exporter(humanName="Sweatshirt Size", export=true, columnName="sweatshirt_size")
     */
    private ?string $sweatshirtSize;
    /**
     * @ORM\Column(name="shirt_size", type="string", nullable=true )
     * @Exporter(humanName="Shirt Size", export=true, columnName="shirt_size")
     */
    private ?string $shirtSize;
    /**
     *  @ORM\Column(name="date_reset_pin", type="date", nullable=true)
     */
    private ?DateTime $dateResetPin;
    /**
     *  @ORM\Column(name="outsource_sub_type", type="string", nullable=true)
     *  @Exporter(humanName="Outsource type", export=true, columnName="outsource_sub_type")
     */
    private ?string $outsourceSubType;
    public function getTokenExpirationSeconds(): int
    {
        return $this->tokenExpirationSeconds;
    }

    public function setTokenExpirationSeconds(int $tokenExpirationSeconds): self
    {
        $this->tokenExpirationSeconds = $tokenExpirationSeconds;
        return $this;
    }

    public function __construct()
    {
        $this->periods = new ArrayCollection();
        $this->employeeEvidences = new ArrayCollection();
        $this->evidenceRequests = new ArrayCollection();
        $this->employeeSkillAreas = new ArrayCollection();
        $this->agreements = new ArrayCollection();
        $this->fAQCategory = new ArrayCollection();
        $this->tribeResponsible = new ArrayCollection();
        $this->leaderStructures = new ArrayCollection();
        $this->leaders = new ArrayCollection();
        $this->contracts = new ArrayCollection();
    }

    public function getUser() : ?User
    {
        return $this->fosUser;
    }

    public function getEmail() : string
    {
        return $this->email;
    }

    public function getSlackType(): string
    {
        return 'AdventureBundle:Employee';
    }

    public function setEmail(string $email) : self
    {
        $this->email = $email;
        return $this;
    }

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

    public function getPhoto() : ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo) : self
    {
        $this->photo = $photo;
        return $this;
    }

    public function getPhone() : ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone) : self
    {
        $this->phone = $phone;
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

    public function getCar() : ?string
    {
        return $this->car;
    }

    public function setCar(?string $car) : self
    {
        $this->car = $car;
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

    public function getWorkMode() : ?int
    {
        return $this->workMode;
    }

    public function setWorkMode(?int $workMode) : self
    {
        $this->workMode = $workMode;
        return $this;
    }

    public function isManager() : bool
    {
        $fosUser = $this->getUser();
        if (is_null($fosUser)) {
            return false;
        }
        return $fosUser->hasRole('ROLE_MANAGER')
            || $fosUser->hasRole('ROLE_TRIBE_MASTER')
            || $fosUser->hasRole('ROLE_SUPER_ADMIN');
    }

    public function isTribeMaster() : bool
    {
        $fosUser = $this->getUser();
        if (is_null($fosUser)) {
            return false;
        }
        return $fosUser->hasRole('ROLE_TRIBE_MASTER')
            || $fosUser->hasRole('ROLE_SUPER_ADMIN');
    }

    public function isSuperAdmin() : bool
    {
        $fosUser = $this->getUser();
        if (is_null($fosUser)) {
            return false;
        }
        return $fosUser->hasRole('ROLE_SUPER_ADMIN');
    }

    public function getHiredAt() : ?DateTime
    {
        return $this->hiredAt;
    }

    public function setHiredAt(?DateTime $hiredAt) : self
    {
        $this->hiredAt = $hiredAt;
        return $this;
    }

    public function getHiredTo() : ?DateTime
    {
        return $this->hiredTo;
    }

    public function setHiredTo(?DateTime $hiredTo) : self
    {
        $this->hiredTo = $hiredTo;
        return $this;
    }

    public function getDateOfBirth() : ?DateTime
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(?DateTime $dateOfBirth) : self
    {
        $this->dateOfBirth = $dateOfBirth;
        return $this;
    }

    public function getDataUpdateTime() : ?DateTime
    {
        return $this->dataUpdate;
    }

    public function setDataUpdateTime(?DateTime $dataUpdate) : self
    {
        $this->dataUpdate = $dataUpdate;
        return $this;
    }

    public function setContractId(?int $id): self
    {
        $this->contract = $id;
        return $this;
    }

    public function getContractId(): ?int
    {
        return $this->contract;
    }

    public function getTribe() : ?Tribe
    {
        return $this->tribe;
    }

    public function setTribe(?Tribe $tribe) : self
    {
        $this->tribe = $tribe;
        return $this;
    }

    public function getPosition() : ?Position
    {
        return $this->position;
    }

    public function setPosition(?Position $position) : self
    {
        $this->position = $position;
        return $this;
    }

    public function getLevel() : ?Level
    {
        return $this->level;
    }

    public function setLevel(?Level $level) : self
    {
        $this->level = $level;
        return $this;
    }

    public function addEmployeeSkillArea(EmployeeSkillArea $employeeSkillArea) : self
    {
        $this->employeeSkillAreas[] = $employeeSkillArea;
        return $this;
    }

    /** @return Collection<int,EmployeeSkillArea> */
    public function getEmployeeSkillAreas() : Collection
    {
        return $this->employeeSkillAreas;
    }

    /** @return Collection<int,FAQCategory> */
    public function getFAQCategory(): Collection
    {
        return $this->fAQCategory;
    }

    /** @return Collection<int,Tribe> */
    public function getTribeResponsible(): Collection
    {
        return $this->tribeResponsible;
    }

    public function __toString() : string
    {
        return $this->name;
    }

    public function getJobTimeValue(): float
    {
        return $this->jobTimeValue;
    }

    public function setJobTimeValue(float $jobTimeValue): self
    {
        $this->jobTimeValue = $jobTimeValue;
        return $this;
    }

    public function isHiddenFromScheduler(): bool
    {
        return false;
    }

    public function getContractType(): ?string
    {
        return self::getContractNameById($this->getContractId());
    }

    public function isFormer() : bool
    {
        if (is_null($this->getHiredTo())) {
            return false;
        }
        $date = $this->getHiredTo()->setTime(23, 59, 59);
        try {
            return new DateTime() > $date;
        } catch (Exception $e) {
            return false;
        }
    }

    /** @return Collection<int,Evidence> */
    public function getEmployeeEvidences(): Collection
    {
        return $this->employeeEvidences;
    }

    /** @return Collection<int,Evidence> */
    public function getEvidenceRequests(): Collection
    {
        return $this->evidenceRequests;
    }

    public function getGender(): ?int
    {
        return $this->gender;
    }

    public function isMale() : bool
    {
        return !is_null($this->gender) && $this->gender === self::GENDER_MALE;
    }

    public function isFemale() : bool
    {
        return !is_null($this->gender) && $this->gender === self::GENDER_FEMALE;
    }

    public function setGender(int $gender): self
    {
        $this->gender = $gender;
        return $this;
    }

    public function getEmergencyFirstName(): ?string
    {
        return $this->emergencyFirstName;
    }

    public function setEmergencyFirstName(string $emergencyFirstName): self
    {
        $this->emergencyFirstName = $emergencyFirstName;
        return $this;
    }

    public function getEmergencyLastName(): ?string
    {
        return $this->emergencyLastName;
    }

    public function setEmergencyLastName(string $emergencyLastName): self
    {
        $this->emergencyLastName = $emergencyLastName;
        return $this;
    }

    public function getEmergencyAddress(): ?string
    {
        return $this->emergencyAddress;
    }

    public function setEmergencyAddress(string $emergencyAddress): self
    {
        $this->emergencyAddress = $emergencyAddress;
        return $this;
    }

    public function getEmergencyPhone(): ?string
    {
        return $this->emergencyPhone;
    }

    public function setEmergencyPhone(string $emergencyPhone): self
    {
        $this->emergencyPhone = $emergencyPhone;
        return $this;
    }

    /** @return Collection<int,LeavePeriod> */
    public function getPeriods() : Collection
    {
        return $this->periods;
    }

    /** @return Collection<int,EmployeeAgreement> */
    public function getAgreements() : Collection
    {
        return $this->agreements;
    }

    public function getHashedPin() : ?string
    {
        return $this->pin;
    }

    public function validatePin(string $pin) : bool
    {
        if (is_null($this->pin)) {
            return false;
        }
        $verification = password_verify($pin, $this->pin);
        if (!$verification) {
            $currentTime = new DateTime();
            $currentTimestamp = $currentTime->getTimestamp();
            $storedTimestamp = ($this->firstFailTime ?? $currentTime)->getTimestamp();
            $diffSeconds = abs($currentTimestamp - $storedTimestamp);
            if (is_null($this->firstFailTime) || $diffSeconds > self::MAX_VERIFICATION_TIME_SECONDS) {
                $this->firstFailTime = $currentTime;
                $this->failCount = 1;
            } else {
                $this->failCount++;
            }
        } else {
            $this->firstFailTime = null;
            $this->failCount = 0;
        }
        if ($this->failCount >= self::MAX_FAIL_COUNT) {
            $this->pinLocked = true;
        }
        return $verification;
    }

    public function setAndHashPin(string $pin) : self
    {
        $this->pin = password_hash($pin, PASSWORD_DEFAULT);
        return $this;
    }

    public function isPinLocked() : bool
    {
        return $this->pinLocked;
    }

    public function isTechTribeLeader() : bool
    {
        return $this->techTribeLeader;
    }

    public function setTechTribeLeader(bool $techTribeLeader): self
    {
        $this->techTribeLeader = $techTribeLeader;
        return $this;
    }

    public function resetLock() : self
    {
        $this->pinLocked = false;
        $this->failCount = 0;
        $this->firstFailTime = null;
        $this->pin = null;
        return $this;
    }

    public function getChildCare(): int
    {
        return $this->childCare;
    }

    public function setChildCare(int $childCare): Employee
    {
        $this->childCare = $childCare;
        return $this;
    }

    public function getGitlabId() : ?int
    {
        return $this->gitlabId;
    }

    public function setGitlabId(?int $gitlabId) : self
    {
        $this->gitlabId = $gitlabId;
        return $this;
    }

    public function getEndingCooperationEntity() : ?EmployeeEndCooperation
    {
        return $this->endingCooperation;
    }

    public function getLanguage() : string
    {
        return $this->language ?? 'en';
    }

    public function setLanguage(string $language) : self
    {
        $this->language = $language;
        return $this;
    }

    public function getSlackLanguage(): string
    {
        return $this->getLanguage() ?? 'en';
    }

    /** @return Collection<int,Employee> */
    public function getLeaders(): Collection
    {
        return $this->leaders;
    }

    /** @return Collection<int,Employee> */
    public function getLeaderStructures(): Collection
    {
        return $this->leaderStructures;
    }

    /**
     * @param int $leaderId
     * @return bool
     */
    public function isLeader(int $leaderId): bool
    {
        $employeeLeaders = $this->getLeaders()->getValues();
        /** @var Employee|null $employeeLeader */
        foreach ($employeeLeaders as $employeeLeader) {
            if ($employeeLeader->getId() === $leaderId) {
                return true;
            }
        }
        return false;
    }

    public function getAvazaId(): ?string
    {
        return $this->avazaId;
    }

    public function setAvazaId(?string $avazaId): Employee
    {
        $this->avazaId = $avazaId;
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

    public function getFirmAddress(): ?string
    {
        return $this->firmAddress;
    }

    public function setFirmAddress(?string $firmAddress): self
    {
        $this->firmAddress = $firmAddress;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmployeeCode(): ?string
    {
        return $this->employeeCode;
    }

    public function setEmployeeCode(?string $employeeCode): self
    {
        $this->employeeCode = $employeeCode;
        return $this;
    }


    public static function getContractIdByName(?string $name): ?int
    {
        if (is_null($name)) {
            return null;
        }
        switch (strtoupper($name)) {
            case 'B2B LUMP SUM':
                return self::CONTRACT_B2B_LUMP_SUM;
            case 'B2B HOURLY':
                return self::CONTRACT_B2B_HOURLY;
            case 'CLC LUMP SUM':
                return self::CONTRACT_CLC_LUMP_SUM;
            case 'CLC HOURLY':
                return self::CONTRACT_CLC_HOURLY;
            case 'CoE':
                return self::CONTRACT_COE;
            case 'OUTSOURCE':
                return self::CONTRACT_OUTSOURCE;
            default:
                return null;
        }
    }

    public static function getContractNameById(?int $id): ?string
    {
        if (is_null($id)) {
            return null;
        }
        switch ($id) {
            case self::CONTRACT_B2B_LUMP_SUM:
                return 'B2B LUMP SUM';
            case self::CONTRACT_B2B_HOURLY:
                return 'B2B HOURLY';
            case self::CONTRACT_CLC_LUMP_SUM:
                return 'CLC LUMP SUM';
            case self::CONTRACT_CLC_HOURLY:
                return 'CLC HOURLY';
            case self::CONTRACT_COE:
                return 'CoE';
            case self::CONTRACT_OUTSOURCE:
                return 'OUTSOURCE';
            default:
                return null;
        }
    }

    public function getCompanyBranch(): string
    {
        return $this->companyBranch ?? 'PL';
    }

    public function setCompanyBranch(string $companyBranch): self
    {
        $this->companyBranch = $companyBranch;
        return $this;
    }

    /** @return Collection<int,Contract> */
    public function getContracts()
    {
        return $this->contracts;
    }

    public function addContract(Contract  $contract): self
    {
        $this->contracts[] = $contract;
        return $this;
    }

    public function getSuperior(): ?Employee
    {
        return $this->superior;
    }

    public function setSuperior(?Employee $superior): self
    {
        $this->superior = $superior;
        return $this;
    }

    public function getFinanceCode(): ?string
    {
        return $this->financeCode;
    }

    public function setFinanceCode(string $financeCode): self
    {
        $this->financeCode = $financeCode;
        return $this;
    }

    public function isStudent(): ?bool
    {
        return $this->student;
    }

    public function setStudent(bool $student): self
    {
        $this->student = $student;
        return $this;
    }

    public function getTaxDeductibleCosts(): ?int
    {
        return $this->taxDeductibleCosts;
    }

    public function setTaxDeductibleCosts(int $taxDeductibleCosts): self
    {
        $this->taxDeductibleCosts = $taxDeductibleCosts;
        return $this;
    }

    public function getWorkStreet(): ?string
    {
        return $this->workStreet;
    }

    public function setWorkStreet(?string $workStreet): self
    {
        $this->workStreet = $workStreet;
        return $this;
    }

    public function getWorkCity(): ?string
    {
        return $this->workCity;
    }

    public function setWorkCity(?string $workCity): self
    {
        $this->workCity = $workCity;
        return $this;
    }

    public function getWorkPostalCode(): ?string
    {
        return $this->workPostalCode;
    }

    public function setWorkPostalCode(?string $workPostalCode): self
    {
        $this->workPostalCode = $workPostalCode;
        return $this;
    }

    public function getWorkCountry(): ?string
    {
        return $this->workCountry;
    }

    public function setWorkCountry(?string $workCountry): self
    {
        $this->workCountry = $workCountry;
        return $this;
    }

    public function getShoeSize(): ?string
    {
        return $this->shoeSize;
    }

    public function setShoeSize(?string $shoeSize): self
    {
        $this->shoeSize = $shoeSize;
        return $this;
    }

    public function getSweatshirtSize(): ?string
    {
        return $this->sweatshirtSize;
    }

    public function setSweatshirtSize(?string $sweatshirtSize): self
    {
        $this->sweatshirtSize = $sweatshirtSize;
        return $this;
    }

    public function getShirtSize(): ?string
    {
        return $this->shirtSize;
    }

    public function setShirtSize(?string $shirtSize): self
    {
        $this->shirtSize = $shirtSize;
        return $this;
    }

    public function getDateResetPin(): ?DateTime
    {
        return $this->dateResetPin;
    }

    public function setDateResetPin(?DateTime $dateResetPin): self
    {
        $this->dateResetPin = $dateResetPin;
        return $this;
    }
    public function setNullAsPin(): self
    {
        $this->pin = null;
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
