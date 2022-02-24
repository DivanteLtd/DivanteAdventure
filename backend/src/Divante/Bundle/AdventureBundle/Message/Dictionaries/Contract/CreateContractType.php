<?php


namespace Divante\Bundle\AdventureBundle\Message\Dictionaries\Contract;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class CreateContractType
{
    use ObjectTrait;

    private string $code;
    private string $name;
    private string $description;
    private bool $active;

    public function __construct(string $code, string $name, string $description, bool $active)
    {
        $this->code = $code;
        $this->name = $name;
        $this->description = $description;
        $this->active = $active;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function isActive(): bool
    {
        return $this->active;
    }
}
