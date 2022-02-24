<?php

namespace Divante\Bundle\AdventureBundle\Entity;

use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Divante\Bundle\AdventureBundle\Entity\Data\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EmployeeSkill
 *
 * @ORM\Table(
 *      name="employee_skill",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="employee_id_skill_id", columns={"employee_id", "skill_id"})
 *      },
 *      indexes={
 *          @ORM\Index(name="FK__skill", columns={"skill_id"}),
 *          @ORM\Index(name="IDX_B630E90E8C03F15C", columns={"employee_id"})
 *      }
 * )
 * @ORM\Entity
 */
class EmployeeSkill
{
    use Timestampable, Id;

    /**
     * Employee skill value
     * @var float|null
     * @ORM\Column(name="value", type="float", precision=10, scale=0, nullable=true)
     */
    private $value = 0;

    /**
     * @var Employee
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Employee")
     * @ORM\JoinColumn(name="employee_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $employee;

    /**
     * @var Skill
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Skill")
     * @ORM\JoinColumn(name="skill_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $skill;

    /**
     * @var SkillArea
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\SkillArea")
     * @ORM\JoinColumn(name="skill_area_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $skillArea;

    public function getValue() : ?float
    {
        return $this->value;
    }

    public function setValue(?float $value) : self
    {
        $this->value = $value;
        return $this;
    }

    public function getEmployee() : Employee
    {
        return $this->employee;
    }

    public function setEmployee(Employee $employee) : self
    {
        $this->employee = $employee;
        return $this;
    }

    public function getSkill() : Skill
    {
        return $this->skill;
    }

    public function setSkill(Skill $skill) : self
    {
        $this->skill = $skill;
        return $this;
    }

    public function getSkillArea() : SkillArea
    {
        return $this->skillArea;
    }

    public function setSkillArea(SkillArea $skillArea) : self
    {
        $this->skillArea = $skillArea;
        return $this;
    }
}
