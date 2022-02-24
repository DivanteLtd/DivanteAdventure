<?php

namespace Divante\Bundle\AdventureBundle\Entity;

use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EmployeeOccupancy
 *
 * @ORM\Table(name="employee_occupancy", uniqueConstraints={
     @ORM\UniqueConstraint(name="unique_entries", columns={"employee_id", "project_id", "date"})
   })
 * @ORM\Entity
 */
class EmployeeOccupancy
{
    use Id;

    /**
     * Employee id
     *
     * @var Employee
     *
     * @Assert\NotBlank()
     *
     * @ORM\ManyToOne(targetEntity="Employee")
     * @ORM\JoinColumn(name="employee_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $employee;

    /**
     * Project id
     *
     * @var Project
     *
     * @Assert\NotBlank()
     *
     * @ORM\ManyToOne(targetEntity="Project")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $project;

    /**
     * Date as Unix timestamp
     * @var integer
     * @Assert\NotBlank()
     * @ORM\Column(name="date", type="integer", nullable=false)
     */
    private $date;

    /**
     * Occupancy value for established job time in project in seconds per day.
     * @var integer
     * @ORM\Column(name="occupancy", type="integer", nullable=false)
     */
    public $occupancy;

    /**
     * Get employee id
     *
     * @return Employee|null
     */
    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    /**
     * Set employee id
     *
     * @param Employee $employee
     *
     * @return EmployeeOccupancy
     */
    public function setEmployee(Employee $employee): EmployeeOccupancy
    {
        $this->employee = $employee;

        return $this;
    }

    /**
     * Get project
     *
     * @return Project
     */
    public function getProject(): ?Project
    {
        return $this->project;
    }

    /**
     * Set project
     *
     * @param Project $project
     *
     * @return EmployeeOccupancy
     */
    public function setProject(Project $project): EmployeeOccupancy
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get date
     *
     * @return integer
     */
    public function getDate(): int
    {
        return $this->date;
    }

    /**
     * Set day's date
     *
     * @param int $date
     *
     * @return EmployeeOccupancy
     */
    public function setDate($date): EmployeeOccupancy
    {
        $this->date = $date;

        return $this;
    }

    public function getOccupancy(): int
    {
        return $this->occupancy;
    }

    public function setOccupancy(int $occupancy): self
    {
        $this->occupancy = $occupancy;
        return $this;
    }

    public function getDateAsDateTime() :\DateTime
    {
        return (new \DateTime())->setTimestamp($this->date);
    }
}
