<?php

namespace Divante\Bundle\AdventureBundle\Message\Employee;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class CreatePotentialEmployee extends AbstractPotentialEmployeeMessage
{
    use ObjectTrait;
    private string $name;
    private string $lastName;
    private ?string $email;
    private string $contractType;
    private int $gender;
    private string $dateOfBirth;
    private string $privatePhone;
    private ?string $city;
    private ?string $postalCode;
    private ?string $street;
    private ?string $country;
    private string $privateEmail;
    private int $recruiterId;
    private ?string $source;
    private ?string $company;
    protected ?string $nip;
    protected ?string $firmName;
    protected ?string $firmAddress;
    protected ?string $outsourceSubType;


    public function __construct(
        string $name,
        string $lastName,
        ?string $email,
        ?int $tribeId,
        ?int $positionId,
        ?string $hireDate,
        string $contractType,
        int $gender,
        string $dateOfBirth,
        string $privatePhone,
        ?string $city,
        ?string $postalCode,
        ?string $street,
        ?string $country,
        string $privateEmail,
        int $recruiterId,
        ?string $source,
        ?string $company,
        ?string $nip,
        ?string $firmName,
        ?string $firmAddress,
        ?string $welcomeDay,
        ?string $language,
        ?string $outsourceSubType
    ) {
        parent::__construct($hireDate, $tribeId, $positionId, $dateOfBirth, $welcomeDay, $language);
        $this->name = $name;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->contractType = $contractType;
        $this->gender = $gender;
        $this->dateOfBirth = $dateOfBirth;
        $this->privatePhone = $privatePhone;
        $this->city = $city;
        $this->postalCode = $postalCode;
        $this->street = $street;
        $this->country = $country;
        $this->privateEmail = $privateEmail;
        $this->recruiterId = $recruiterId;
        $this->source = $source;
        $this->company = $company;
        $this->nip = $nip;
        $this->firmName = $firmName;
        $this->firmAddress = $firmAddress;
        $this->outsourceSubType = $outsourceSubType;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getLastName() : string
    {
        return $this->lastName;
    }

    public function getEmail() : ?string
    {
        return $this->email;
    }

    public function getContractType() : string
    {
        return $this->contractType;
    }

    public function getGender() : int
    {
        return $this->gender;
    }

    public function getPrivatePhone() : string
    {
        return $this->privatePhone;
    }

    public function getCity() : ?string
    {
        return $this->city;
    }

    public function getPostalCode() : ?string
    {
        return $this->postalCode;
    }

    public function getStreet() : ?string
    {
        return $this->street;
    }

    public function getCountry() : ?string
    {
        return $this->country;
    }

    public function getPrivateEmail() : string
    {
        return $this->privateEmail;
    }

    public function getRecruiterId() : int
    {
        return $this->recruiterId;
    }

    public function getSource() : ?string
    {
        return $this->source;
    }

    public function getCompany() : ?string
    {
        return $this->company;
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

    public function getOutsourceSubType(): ?string
    {
        return $this->outsourceSubType;
    }
}
