<?php

namespace Divante\Bundle\AdventureBundle\Events;

use Divante\Bundle\AdventureBundle\Entity\Project;
use Symfony\Component\EventDispatcher\Event;

class ProjectOccupancyEvent extends Event
{
    private Project $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function getProject(): Project
    {
        return $this->project;
    }
}
