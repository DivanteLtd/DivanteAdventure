<?php

namespace Tests\Entrypoints\Api\Projects;

use Divante\Bundle\AdventureBundle\Entity\Project;
use Tests\Entrypoints\AbstractEntrypointTest;

abstract class AbstractProjectsTest extends AbstractEntrypointTest
{
    public function generateProject() : Project
    {
        $project = new Project();
        $project->setName("RandomProjectName".rand(0, 10000))
            ->setCreatedAt()
            ->setUpdatedAt();
        $this->em->persist($project);
        return $project;
    }
}