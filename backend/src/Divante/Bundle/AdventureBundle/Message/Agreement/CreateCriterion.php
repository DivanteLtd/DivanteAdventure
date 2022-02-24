<?php


namespace Divante\Bundle\AdventureBundle\Message\Agreement;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class CreateCriterion
{
    use ObjectTrait;

    private string $namePl;
    private string $nameEn;

    public function __construct(string $namePl, string $nameEn)
    {
        $this->namePl = $namePl;
        $this->nameEn = $nameEn;
    }

    public function getNamePl() : string
    {
        return $this->namePl;
    }
    public function getNameEn() : string
    {
        return $this->nameEn;
    }
}
