<?php

namespace Divante\Bundle\AdventureBundle\Message\Checklist;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class EditChecklistTemplate
{
    use ObjectTrait;

    private int $id;
    private ?string $namePl;
    private ?string $nameEn;

    public function __construct(int $id, ?string $namePl, ?string $nameEn)
    {
        $this->id = $id;
        $this->namePl = $namePl;
        $this->nameEn = $nameEn;
    }

    public function getId() : int
    {
        return $this->id;
    }

    public function getNamePl(): ?string
    {
        return $this->namePl;
    }

    public function getNameEn(): ?string
    {
        return $this->nameEn;
    }
}
