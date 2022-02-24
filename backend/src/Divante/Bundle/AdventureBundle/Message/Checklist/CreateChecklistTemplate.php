<?php

namespace Divante\Bundle\AdventureBundle\Message\Checklist;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class CreateChecklistTemplate
{
    use ObjectTrait;

    private int $type;
    private string $namePl;
    private string $nameEn;

    public function __construct(int $type, string $namePl, string $nameEn)
    {
        $this->type = $type;
        $this->namePl = $namePl;
        $this->nameEn = $nameEn;
    }

    public function getType(): int
    {
        return $this->type;
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
