<?php

namespace Divante\Bundle\AdventureBundle\Entity;

use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Divante\Bundle\AdventureBundle\Entity\Data\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EmployeeSkillArea
 *
 * @ORM\Table(
 *      name="employee_skill_area",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="employee_id_skill_area_id", columns={"employee_id", "skill_area_id"})
 *      },
 *      indexes={
 *          @ORM\Index(name="FK__skill_area", columns={"skill_area_id"}),
 *          @ORM\Index(name="IDX_802110E8C03F15C", columns={"employee_id"})
 *      }
 * )
 * @ORM\Entity
 */
class EmployeeSkillArea
{
    use Timestampable, Id;

    /**
     * Employee skill area averaged value from required skills
     * @var float|null
     * @ORM\Column(name="value_averaged", type="float", precision=10, scale=0, nullable=true)
     */
    private $valueAveraged = 0;

    /**
     * @var SkillArea
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\SkillArea")
     * @ORM\JoinColumn(name="skill_area_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $skillArea;

    /**
     * @var Employee
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Employee", inversedBy="employeeSkillAreas")
     * @ORM\JoinColumn(name="employee_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $employee;

    public function getValueAveraged() : ?float
    {
        return $this->valueAveraged;
    }

    public function setValueAveraged(?float $valueAveraged) : self
    {
        $this->valueAveraged = $valueAveraged;

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
