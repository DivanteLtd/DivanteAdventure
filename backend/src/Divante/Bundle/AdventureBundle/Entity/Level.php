<?php

namespace Divante\Bundle\AdventureBundle\Entity;

use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Divante\Bundle\AdventureBundle\Entity\Data\Timestampable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Level
 * @package Divante\Bundle\AdventureBundle\Entity
 * @ORM\Entity
 * @ORM\Table(name="level")
 */
class Level
{
    use Id, Timestampable;

    /**
     * @var Collection<int,Employee>
     * @ORM\OneToMany(targetEntity="Divante\Bundle\AdventureBundle\Entity\Employee", mappedBy="level")
     */
    private Collection $employees;

    /**
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\LevelingStrategy", inversedBy="levels")
     * @ORM\JoinColumn(name="strategy_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private LevelingStrategy $strategy;

    /** @ORM\Column(name="priority", type="integer", nullable=false) */
    private int $priority = 0;
    /** @ORM\Column(name="name", type="string", length=50, nullable=false) */
    private string $name = '';

    public function __construct()
    {
        $this->employees = new ArrayCollection();
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

    public function getPriority() : int
    {
        return $this->priority;
    }

    public function setPriority(int $priority) : self
    {
        $this->priority = $priority;
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
