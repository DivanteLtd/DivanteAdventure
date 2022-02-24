<?php

namespace Divante\Bundle\AdventureBundle\Services;

use Divante\Bundle\AdventureBundle\Entity\Project;
use Divante\Bundle\AdventureBundle\Message\Project\UpdateProject;

class IntegrationPairingsUpdate
{
    /** @var GitLab\UpdatePairings */
    private $gitlab;

    public function __construct(GitLab\UpdatePairings $gitlab)
    {
        $this->gitlab = $gitlab;
    }

    public function update(Project $project, UpdateProject $message) : void
    {
        $this->gitlab->update($project, $message);
    }
}
