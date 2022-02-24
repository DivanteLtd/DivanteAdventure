<?php

namespace Divante\Bundle\AdventureBundle\Entity;

use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Divante\Bundle\AdventureBundle\Entity\Data\NamedEntity;
use Divante\Bundle\AdventureBundle\Entity\Data\Timestampable;
use Doctrine\ORM\Mapping as ORM;

/**
 * SkillArea
 *
 * @ORM\Table(name="skill_area")
 * @ORM\Entity
 */
class SkillArea implements NamedEntity
{
    use Timestampable, Id;

    /** @ORM\Column(name="description", type="text", length=65535, nullable=true) */
    private ?string $description;
    /** @ORM\Column(name="value_averaged", type="float", precision=10, scale=0, nullable=true) */
    private ?float $valueAveraged = 0;
    /** @ORM\Column(name="name", type="string", length=50, nullable=false) */
    private string $name = '';

    public function getDescription() : ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description) : self
    {
        $this->description = $description;
        return $this;
    }

    public function getValueAveraged() : ?float
    {
        return $this->valueAveraged;
    }

    public function setValueAveraged(?float $valueAveraged) : self
    {
        $this->valueAveraged = $valueAveraged;
        return $this;
    }

    public function __toString() : string
    {
        return $this->name;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function setName(string $name) : self
    {
        $this->name = $name;
        return $this;
    }
}
