<?php

namespace Divante\Bundle\AdventureBundle\Message\Agreement;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class UpdateCriterion
{
    use ObjectTrait;

    private string $namePl;
    private string $nameEn;
    private int $id;

    public function __construct(string $namePl, string $nameEn, int $id)
    {
        $this->namePl = $namePl;
        $this->nameEn = $nameEn;
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNamePl(): string
    {
        return $this->namePl;
    }

    public function getNameEn(): string
    {
        return $this->nameEn;
    }
}
