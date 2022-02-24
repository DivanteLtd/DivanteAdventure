<?php

namespace Divante\Bundle\AdventureBundle\Events\Integration;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\GitlabProject;
use Divante\Bundle\AdventureBundle\Entity\Project;

abstract class AbstractGitlabAccessEvent extends AbstractEmployeeProjectAccessEvent
{
    private GitlabProject $gitlabProject;

    public function __construct(Employee $employee, Project $project, GitlabProject $gitlabProject)
    {
        parent::__construct($employee, $project);
        $this->gitlabProject = $gitlabProject;
    }

    public function getGitlabProject() : GitlabProject
    {
        return $this->gitlabProject;
    }
}
