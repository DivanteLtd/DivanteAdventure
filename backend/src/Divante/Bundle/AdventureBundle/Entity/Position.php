<?php

namespace Divante\Bundle\AdventureBundle\Entity;

use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Divante\Bundle\AdventureBundle\Entity\Data\NamedEntity;
use Divante\Bundle\AdventureBundle\Entity\Data\Timestampable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Position
 * @package Divante\Bundle\AdventureBundle\Entity
 * @ORM\Entity
 * @ORM\Table(name="position")
 */
class Position implements NamedEntity
{
    use Timestampable, Id;

    /**
     * @var Collection<int,Tribe>
     * @ORM\ManyToMany(targetEntity="Divante\Bundle\AdventureBundle\Entity\Tribe", inversedBy="positions")
     * @ORM\JoinTable(name="position_tribe",
     *     joinColumns={@ORM\JoinColumn(name="position_id", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="tribe_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    private Collection $tribes;

    /**
     * @var Collection<int,Employee>
     * @ORM\OneToMany(targetEntity="Divante\Bundle\AdventureBundle\Entity\Employee", mappedBy="position")
     */
    private Collection $employees;

    /**
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\LevelingStrategy", inversedBy="positions")
     * @ORM\JoinColumn(name="strategy_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private LevelingStrategy $strategy;
    /** @ORM\Column(name="name", type="string", length=50, nullable=false) */
    private string $name = '';

    public function __construct()
    {
        $this->tribes = new ArrayCollection();
        $this->employees = new ArrayCollection();
    }

    /** @return Collection<int,Tribe> */
    public function getTribes() : Collection
    {
        return $this->tribes;
    }

    /** @return Collection<int,Employee> */
    public function getEmployees() : Collection
    {
        return $this->employees;
    }

    public function getStrategy() : LevelingStrategy
    {
        return $this->strategy;
    }

    public function setStrategy(LevelingStrategy $strategy) : self
    {
        $this->strategy = $strategy;
        return $this;
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
