<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\Query\Agreement;

class MarketingAcceptationView
{
    protected string $name;
    /** @var string */
    protected $lastName;
    /** @var string */
    protected string $email;
    /** @var string[] */
    protected array $description;

    /**
     * GDPRAcceptationView constructor.
     * @param string $name
     * @param string $lastName
     * @param string $email
     * @param string[] $description
     */
    public function __construct(string $name, string $lastName, string $email, array $description)
    {
        $this->name = $name;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->description = $description;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
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
}
