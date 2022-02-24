<?php
/**
 * Created by PhpStorm.
 * User: pawel
 * Date: 14.03.19
 * Time: 10:54
 */

namespace Divante\Bundle\AdventureBundle\Message\Employee;

use DateTime;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;
use Divante\Bundle\AdventureBundle\Services\Structure\StructureException;

class UpdateEmployee
{
    use ObjectTrait;

    protected int $id;
    protected ?string $firstName;
    protected ?string $lastName;
    protected ?string $email;
    protected ?string $photoUrl;
    protected ?string $privatePhone;
    protected ?string $companyPhone;
    protected ?string $city;
    protected ?string $postalCode;
    protected ?string $street;
    protected ?string $country;
    protected ?int $contractId;
    protected ?DateTime $hiredAt;
    protected ?string $licencePlate;
    protected ?bool $manager;
    protected ?int $positionId;
    protected ?int $levelId;
    protected ?int $workMode;
    protected ?int $tribeId;
    protected ?string $emergencyFirstName;
    protected ?string $emergencyLastName;
    protected ?string $emergencyAddress;
    protected ?string $emergencyPhone;
    protected ?int $gender;
    /** @var string[]|null */
    protected ?array $roles;
    protected ?float $jobTimeValue;
    protected ?string $pin;
    protected ?string $oldPin;
    protected ?DateTime $dateOfBirth;
    protected ?int $childCare;
    protected ?string $language;
    protected ?string $nip;
    protected ?string $firmName;
    protected ?string $firmAddress;
    private ?Employee $callingEmployee = null;
    protected ?DateTime $dataUpdate;
    protected ?string $employeeCode;
    protected ?bool $student;
    protected ?int $taxDeductibleCosts;
    protected ?string $workStreet;
    protected ?string $workCity;
    protected ?string $workPostalCode;
    protected ?string $workCountry;
    protected ?string $financeCode;
    protected ?string $superiorEmail;
    protected ?string $shoeSize;
    protected ?string $sweatshirtSize;
    protected ?string $shirtSize;
    protected ?string $subTypeContract;

    public function __construct(
        int $id,
        ?string $firstName,
        ?string $lastName,
        ?string $email,
        ?string $photoUrl,
        ?string $privatePhone,
        ?string $companyPhone,
        ?string $city,
        ?string $postalCode,
        ?string $street,
        ?string $country,
        ?int $contractId,
        ?DateTime $hiredAt,
        ?string $licencePlate,
        ?bool $manager,
        ?int $positionId,
        ?int $levelId,
        ?int $workMode,
        ?int $tribeId,
        ?string $emergencyFirstName,
        ?string $emergencyLastName,
        ?string $emergencyAddress,
        ?string $emergencyPhone,
        ?int $gender,
        ?array $roles,
        ?float $jobTimeValue,
        ?string $pin,
        ?string $oldPin,
        ?DateTime $dateOfBirth,
        ?int $childCare,
        ?string $language,
        ?string $nip,
        ?string $firmName,
        ?string $firmAddress,
        ?DateTime $dataUpdate,
        ?string $employeeCode,
        ?bool $student,
        ?int $taxDeductibleCosts,
        ?string $workStreet,
        ?string $workCity,
        ?string $workPostalCode,
        ?string $workCountry,
        ?string $financeCode,
        ?string $superiorEmail,
        ?string $shoeSize,
        ?string $sweatshirtSize,
        ?string $shirtSize,
        ?string $subTypeContract
    ) {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->photoUrl = $photoUrl;
        $this->privatePhone = $privatePhone;
        $this->companyPhone = $companyPhone;
        $this->city = $city;
        $this->postalCode = $postalCode;
        $this->street = $street;
        $this->country = $country;
        $this->contractId = $contractId;
        $this->hiredAt = $hiredAt;
        $this->licencePlate = $licencePlate;
        $this->manager = $manager;
        $this->positionId = $positionId;
        $this->levelId = $levelId;
        $this->workMode = $workMode;
        $this->tribeId = $tribeId;
        $this->emergencyFirstName = $emergencyFirstName;
        $this->emergencyLastName = $emergencyLastName;
        $this->emergencyAddress = $emergencyAddress;
        $this->emergencyPhone = $emergencyPhone;
        $this->gender = $gender;
        $this->roles = $roles;
        $this->jobTimeValue = $jobTimeValue;
        $this->pin = $pin;
        $this->oldPin = $oldPin;
        $this->dateOfBirth = $dateOfBirth;
        $this->childCare = $childCare;
        $this->language = $language;
        $this->nip = $nip;
        $this->firmName = $firmName;
        $this->firmAddress = $firmAddress;
        $this->dataUpdate = $dataUpdate;
        $this->employeeCode = $employeeCode;
        $this->student = $student;
        $this->taxDeductibleCosts = $taxDeductibleCosts;
        $this->workCity = $workCity;
        $this->workCountry = $workCountry;
        $this->workPostalCode = $workPostalCode;
        $this->workStreet = $workStreet;
        $this->financeCode = $financeCode;
        $this->superiorEmail = $superiorEmail;
        $this->shoeSize = $shoeSize;
        $this->sweatshirtSize = $sweatshirtSize;
        $this->shirtSize = $shirtSize;
        $this->subTypeContract = $subTypeContract;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPhotoUrl(): ?string
    {
        return $this->photoUrl;
    }

    public function getPrivatePhone(): ?string
    {
        return $this->privatePhone;
    }

    public function getCompanyPhone(): ?string
    {
        return $this->companyPhone;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function getContractId(): ?int
    {
        return $this->contractId;
    }

    public function getHiredAt(): ?DateTime
    {
        return $this->hiredAt;
    }

    public function getLicencePlate(): ?string
    {
        return $this->licencePlate;
    }

    public function getManager(): ?bool
    {
        return $this->manager;
    }

    public function getPositionId(): ?int
    {
        return $this->positionId;
    }

    public function getLevelId() : ?int
    {
        return $this->levelId;
    }

    public function getWorkMode(): ?int
    {
        return $this->workMode;
    }

    public function getTribeId(): ?int
    {
        return $this->tribeId;
    }

    public function getEmergencyFirstName(): ?string
    {
        return $this->emergencyFirstName;
    }

    public function getEmergencyLastName(): ?string
    {
        return $this->emergencyLastName;
    }

    public function getEmergencyAddress(): ?string
    {
        return $this->emergencyAddress;
    }

    public function getEmergencyPhone(): ?string
    {
        return $this->emergencyPhone;
    }

    public function getGender(): ?int
    {
        return $this->gender;
    }

    /** @return string[]|null */
    public function getRoles() : ?array
    {
        return $this->roles;
    }

    public function getJobTimeValue() : ?float
    {
        return $this->jobTimeValue;
    }

    public function getPin() : ?string
    {
        return $this->pin;
    }

    public function getOldPin() : ?string
    {
        return $this->oldPin;
    }

    public function getDateOfBirth() : ?DateTime
    {
        return $this->dateOfBirth;
    }

    public function getChildCare() : ?int
    {
        return $this->childCare;
    }

    public function getLanguage() : ?string
    {
        return $this->language;
    }

    public function getNip(): ?string
    {
        return $this->nip;
    }

    public function getFirmName(): ?string
    {
        return $this->firmName;
    }

    public function getFirmAddress(): ?string
    {
        return $this->firmAddress;
    }

    public function setCallingEmployee(?Employee $callingEmployee): void
    {
        $this->callingEmployee = $callingEmployee;
    }

    public function getCallingEmployee(): ?Employee
    {
        return $this->callingEmployee;
    }

    public function getDataUpdate(): ?DateTime
    {
        return $this->dataUpdate;
    }

    public function getEmployeeCode(): ?string
    {
        return $this->employeeCode;
    }

    public function isStudent(): ?bool
    {
        return $this->student;
    }

    public function getTaxDeductibleCosts(): ?int
    {
        return $this->taxDeductibleCosts;
    }

    public function getWorkStreet(): ?string
    {
        return $this->workStreet;
    }

    public function getWorkCity(): ?string
    {
        return $this->workCity;
    }

    public function getWorkPostalCode(): ?string
    {
        return $this->workPostalCode;
    }

    public function getWorkCountry(): ?string
    {
        return $this->workCountry;
    }

    public function getFinanceCode(): ?string
    {
        return $this->financeCode;
    }

    public function getSuperiorEmail(): ?string
    {
        return $this->superiorEmail;
    }

    public function getShoeSize(): ?string
    {
        return $this->shoeSize;
    }

    public function getSweatshirtSize(): ?string
    {
        return $this->sweatshirtSize;
    }

    public function getShirtSize(): ?string
    {
        return $this->shirtSize;
    }

    public function getSubTypeContract(): ?string
    {
        return $this->subTypeContract;
    }
}
