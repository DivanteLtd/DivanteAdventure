<?php

namespace Divante\Bundle\AdventureBundle\Entity;

use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Divante\Bundle\AdventureBundle\Entity\Data\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EmployeeSkillHistory
 *
 * @ORM\Table(
 *      name="employee_skill_history",
 *      indexes={
 *          @ORM\Index(name="FK_employee_skill_history_employee", columns={"employee_id"}),
 *          @ORM\Index(name="FK_employee_skill_history_skill", columns={"skill_id"})
 *      }
 * )
 * @ORM\Entity
 */
class EmployeeSkillHistory
{
    use Timestampable, Id;

    /**
     * Employee's skill old value
     * @var float
     * @Assert\NotBlank()
     * @ORM\Column(name="value_old", type="float", precision=10, scale=0, nullable=false)
     */
    private $valueOld = 0;

    /**
     * Employee's skill new value
     * @var float
     * @Assert\NotBlank()
     * @ORM\Column(name="value_new", type="float", precision=10, scale=0, nullable=false)
     */
    private $valueNew = 0;

    /**
     * @var Skill
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Skill")
     * @ORM\JoinColumn(name="skill_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $skill;

    /**
     * @var Employee
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Employee")
     * @ORM\JoinColumn(name="employee_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $employee;

    public function getValueOld() : float
    {
        return $this->valueOld;
    }

    public function setValueOld(float $valueOld) : self
    {
        $this->valueOld = $valueOld;
        return $this;
    }

    public function getValueNew() : float
    {
        return $this->valueNew;
    }

    public function setValueNew(float $valueNew) : self
    {
        $this->valueNew = $valueNew;
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

    public function getEmployee() : Employee
    {
        return $this->employee;
    }

    public function setEmployee(Employee $employee) : self
    {
        $this->employee = $employee;
        return $this;
    }
}
