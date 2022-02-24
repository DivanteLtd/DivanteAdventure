<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\Query\Agreement;

class ISOAcceptationView
{
    protected string $name;
    protected string $lastName;
    protected string $email;
    /** @var string[] */
    protected array $description;
    protected ?string $updatedAt;

    /**
     * ISOAcceptationView constructor.
     * @param string $name
     * @param string $lastName
     * @param string $email
     * @param string[] $description
     * @param null|string $updatedAt
     */
    public function __construct(string $name, string $lastName, string $email, array $description, ?string $updatedAt)
    {
        $this->name = $name;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->description = $description;
        $this->updatedAt = $updatedAt;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    /** @return string[] */
    public function getDescription(): array
    {
        return $this->description;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }
}
