<?php

namespace Divante\Bundle\AdventureBundle\Message\FAQ;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class UpdateFAQCategory
{
    use ObjectTrait;

    private int $id;
    private string $namePl;
    private string $nameEn;
    /** @var int[] */
    private array $employeeIds;

    /**
     * UpdateFAQCategory constructor.
     * @param int $id
     * @param int[] $employeeIds
     * @param string $namePL
     * @param string $nameEn
     */
    public function __construct(int $id, array $employeeIds, string $namePL, string $nameEn)
    {
        $this->id = $id;
        $this->employeeIds = $employeeIds;
        $this->namePl = $namePL;
        $this->nameEn = $nameEn;
    }

    public function getId() : int
    {
        return $this->id;
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
