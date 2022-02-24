<?php

namespace Divante\Bundle\AdventureBundle\Entity;

use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Divante\Bundle\AdventureBundle\Entity\Data\Timestampable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class LevelingStrategy
 * @package Divante\Bundle\AdventureBundle\Entity
 * @ORM\Entity
 * @ORM\Table(name="leveling_strategy")
 */
class LevelingStrategy
{
    use Id, Timestampable;

    /**
     * @var Collection<int,Level>
     * @ORM\OneToMany(targetEntity="Divante\Bundle\AdventureBundle\Entity\Level", mappedBy="strategy")
     */
    private Collection $levels;

    /**
     * @var Collection<int,Position>
     * @ORM\OneToMany(targetEntity="Divante\Bundle\AdventureBundle\Entity\Position", mappedBy="strategy")
     */
    private Collection $positions;
    /** @ORM\Column(name="name", type="string", length=50, nullable=false) */
    private string $name = '';

    public function __construct()
    {
        $this->levels = new ArrayCollection();
        $this->positions = new ArrayCollection();
    }

    /** @return Collection<int,Level> */
    public function getLevels() : Collection
    {
        return $this->levels;
    }

    /** @return Collection<int,Position> */
    public function getPositions() : Collection
    {
        return $this->positions;
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
