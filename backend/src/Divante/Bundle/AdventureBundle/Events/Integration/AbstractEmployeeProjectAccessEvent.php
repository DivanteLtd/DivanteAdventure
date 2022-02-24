<?php
namespace Divante\Bundle\AdventureBundle\Events\Integration;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\GitlabProject;
use Divante\Bundle\AdventureBundle\Entity\Project;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractEmployeeProjectAccessEvent extends Event
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

    /**
     * @param Employee $employee
     * @param Project $project
     * @param bool $add
     * @return AbstractEmployeeProjectAccessEvent[]
     */
    public static function createEvents(Employee $employee, Project $project, bool $add) : array
    {
        /** @var GitlabProject[] $gitlabProjects */
        $gitlabProjects = $project->getGitlabProjects()->toArray();
        /** @var AbstractEmployeeProjectAccessEvent[] $result */
        $result = [];

        /** @var GitlabProject $gitlabProject */
        foreach ($gitlabProjects as $gitlabProject) {
            if ($add) {
                $result[] = new AddGitlabAccessEvent($employee, $project, $gitlabProject);
            } else {
                $result[] = new RemoveGitlabAccessEvent($employee, $project, $gitlabProject);
            }
        }

        return $result;
    }
}
