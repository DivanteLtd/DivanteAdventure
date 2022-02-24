<?php

namespace Divante\Bundle\AdventureBundle\Entity;

use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Divante\Bundle\AdventureBundle\Entity\Data\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EmployeeProject
 *
 * @ORM\Table(
 *     name="employee_project",
 *     uniqueConstraints={
 *      @ORM\UniqueConstraint(name="employee_id_project_id", columns={"employee_id", "project_id"})
 *     },
 *     indexes={
 *       @ORM\Index(name="FK_employee_project_project", columns={"project_id"}),
 *       @ORM\Index(name="IDX_AFFF86E18C03F15C", columns={"employee_id"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="Divante\Bundle\AdventureBundle\Entity\Repository\EmployeeProjectRepository")
 */
class EmployeeProject
{
    use Timestampable, Id;

    /**
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Project")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private Project $project;

    /**
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Employee")
     * @ORM\JoinColumn(name="employee_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private Employee $employee;

    /**
     * @var string[]
     * @ORM\Column(name="date_from", type="array", nullable=false)
     */
    private array $dateFrom = [];

    /**
     * @var string[]
     * @ORM\Column(name="date_to", type="array", nullable=false)
     */
    private array $dateTo = [];

    /** @ORM\Column(name="overtime", type="boolean", options={"default" : false}) */
    private bool $overtime = false;

    public function getProject(): Project
    {
        return $this->project;
    }

    public function setProject(?Project $project = null): EmployeeProject
    {
        $this->project = $project;
        return $this;
    }

    public function getEmployee(): Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee = null): EmployeeProject
    {
        $this->employee = $employee;
        return $this;
    }

    /** @return string[] */
    public function getDateFrom(): array
    {
        return $this->dateFrom;
    }

    /**
     * @param string[] $dateFrom
     * @return EmployeeProject
     */
    public function setDateFrom(array $dateFrom): EmployeeProject
    {
        $this->dateFrom = $dateFrom;
        return $this;
    }

    /** @return string[] */
    public function getDateTo(): array
    {
        return $this->dateTo;
    }

    /**
     * @param string[] $dateTo
     * @return EmployeeProject
     */
    public function setDateTo(array $dateTo): EmployeeProject
    {
        $this->dateTo = $dateTo;
        return $this;
    }

    public function isOvertime(): bool
    {
        return $this->overtime;
    }

    public function setOvertime(bool $overtime): EmployeeProject
    {
        $this->overtime = $overtime;
        return $this;
    }

    public function __toString() : string
    {
        return serialize($this);
    }
}
