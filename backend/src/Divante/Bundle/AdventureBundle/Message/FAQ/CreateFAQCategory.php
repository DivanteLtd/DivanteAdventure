<?php

namespace Divante\Bundle\AdventureBundle\Message\FAQ;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class CreateFAQCategory
{
    use ObjectTrait;

    private string $namePl;
    private string $nameEn;
    /** @var int[] */
    private array $employeeIds;

    /**
     * CreateFAQCategory constructor.
     * @param int[] $employeeIds
     * @param string $namePL
     * @param string $nameEn
     */
    public function __construct(array $employeeIds, string $namePL, string $nameEn)
    {
        $this->employeeIds = $employeeIds;
        $this->namePl = $namePL;
        $this->nameEn = $nameEn;
    }

    /** @return int[] */
    public function getEmployees() : array
    {
        return $this->employeeIds;
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
