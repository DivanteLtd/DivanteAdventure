<?php

namespace Divante\Bundle\AdventureBundle\Entity\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Divante\Bundle\AdventureBundle\Entity\Data\Timestampable;

trait ChecklistTrait
{
    use Id, Timestampable;

    /** @ORM\Column(name="type", type="smallint") */
    private int $type;
    /** @ORM\Column(name="name_pl", type="string") */
    private string $namePl;
    /** @ORM\Column(name="name_en", type="string") */
    private string $nameEn;

    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return $this
     */
    public function setType(int $type)
    {
        $this->type = $type;
        return $this;
    }

    public function getNamePl(): string
    {
        return $this->namePl;
    }

    /**
     * @param string $namePl
     * @return $this
     */
    public function setNamePl(string $namePl)
    {
        $this->namePl = $namePl;
        return $this;
    }

    public function getNameEn(): string
    {
        return $this->nameEn;
    }

    /**
     * @param string $nameEn
     * @return $this
     */
    public function setNameEn(string $nameEn)
    {
        $this->nameEn = $nameEn;
        return $this;
    }
}
