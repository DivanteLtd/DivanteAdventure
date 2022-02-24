<?php
namespace Divante\Bundle\AdventureBundle\Events;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Project;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractEmployeeProjectEvent extends Event
{
    private Employee $employee;
    private Project $project;

    public function __construct(Employee $employee, Project $project)
    {
        $this->employee = $employee;
        $this->project = $project;
    }

    public function getEmployee() : Employee
    {
        return $this->employee;
    }

    public function getProject() : Project
    {
        return $this->project;
    }
}
