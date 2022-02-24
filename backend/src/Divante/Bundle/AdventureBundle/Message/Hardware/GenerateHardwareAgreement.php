<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\Message\Hardware;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class GenerateHardwareAgreement
{
    use ObjectTrait;

    private int $id;
    private string $generatedPassword;
    private string $name;
    private string $lastName;
    private string $manufacturer;
    private string $model;
    private string $serialNumber;
    private ?string $PESEL;
    private ?string $NIP;
    private ?string $company;
    private ?string $headquarters;
    /** @var string[] */
    private array $languages;

    /**
     * @param int $id
     * @param string $generatedPassword
     * @param string $name
     * @param string $lastName
     * @param string $manufacturer
     * @param string $model
     * @param string $serialNumber
     * @param string|null $PESEL
     * @param string|null $NIP
     * @param string|null $company
     * @param string|null $headquarters
     * @param string[] $languages
     */
    public function __construct(
        int $id,
        string $generatedPassword,
        string $name,
        string $lastName,
        string $manufacturer,
        string $model,
        string $serialNumber,
        ?string $PESEL,
        ?string $NIP,
        ?string $company,
        ?string $headquarters,
        array $languages
    ) {
        $this->id = $id;
        $this->generatedPassword = $generatedPassword;
        $this->name = $name;
        $this->lastName = $lastName;
        $this->manufacturer = $manufacturer;
        $this->model = $model;
        $this->serialNumber = $serialNumber;
        $this->PESEL = $PESEL;
        $this->NIP = $NIP;
        $this->company = $company;
        $this->headquarters = $headquarters;
        $this->languages = $languages;
    }

    public function getId() : int
    {
        return $this->id;
    }

    public function getPassword() : string
    {
        return $this->generatedPassword;
    }

    public function getName(): string
    {
        return $this->name;
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

    public function getPESEL(): ?string
    {
        return $this->PESEL;
    }

    public function getNIP(): ?string
    {
        return $this->NIP;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function getHeadquarters(): ?string
    {
        return $this->headquarters;
    }

    /** @return string[] */
    public function getLanguages(): array
    {
        return $this->languages;
    }
}
