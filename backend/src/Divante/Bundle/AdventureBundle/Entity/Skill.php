<?php

namespace Divante\Bundle\AdventureBundle\Entity;

use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Divante\Bundle\AdventureBundle\Entity\Data\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Skill
 *
 * @ORM\Table(
 *      name="skill",
 *      indexes={
 *          @ORM\Index(name="FK_skill_skill_area", columns={"skill_area_id"}),
 *          @ORM\Index(name="FK_skill_employee", columns={"employee_id"})
 *      }
 * )
 * @ORM\Entity
 */
class Skill
{
    use Timestampable, Id;

    /**
     * Skill name
     * @var string
     * @Assert\NotBlank
     * @Assert\Length(min=1, max=50)
     * @ORM\Column(name="name", type="string", length=80, nullable=false)
     */
    private $name;

    /**
     * Skill description
     * @var string|null
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * Is standard required skill
     * @var int|null
     * @ORM\Column(name="required", type="integer", nullable=true)
     */
    private $required;

    /**
     * Employees skill area averaged value
     * @var float|null
     * @ORM\Column(name="value_averaged", type="float", precision=10, scale=0, nullable=true)
     */
    private $valueAveraged = 0;

    /**
     * Skill area id
     * @var \Divante\Bundle\AdventureBundle\Entity\SkillArea|null
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\SkillArea")
     * @ORM\JoinColumn(name="skill_area_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $skillArea;

    /**
     * Employee id
     * @var \Divante\Bundle\AdventureBundle\Entity\Employee|null
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Employee")
     * @ORM\JoinColumn(name="employee_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $employee;

    public function getName() : string
    {
        return $this->name;
    }

    public function setName(string $name) : self
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription() : ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description) : self
    {
        $this->description = $description;
        return $this;
    }

    public function getRequired() : ?int
    {
        return $this->required;
    }

    public function setRequired(?int $value) : self
    {
        $this->required = $value;
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

    public function getSkillArea() : ?SkillArea
    {
        return $this->skillArea;
    }

    public function setSkillArea(?SkillArea $skillArea) : self
    {
        $this->skillArea = $skillArea;
        return $this;
    }

    public function getEmployee() : ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee) : self
    {
        $this->employee = $employee;
        return $this;
    }

    public function __toString() : string
    {
        return $this->name;
    }
}
