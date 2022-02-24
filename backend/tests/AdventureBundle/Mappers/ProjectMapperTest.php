<?php

namespace Tests\AdventureBundle\Mappers;

use Divante\Bundle\AdventureBundle\Entity\Project;
use Divante\Bundle\AdventureBundle\Mappers\Mapper;
use Divante\Bundle\AdventureBundle\Mappers\ProjectMapper;
use Tests\FoundationTestCase;

class ProjectMapperTest extends FoundationTestCase
{

    public function testMappingData() : void
    {
        $projectId = rand(0, 1000000);
        $projectName = "RandomName".rand(0, 1000000);
        $project = $this->createProjectWithId($projectId)
            ->setName($projectName);
        $array = $this->map($project);

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('name', $array);

        $this->assertEquals($projectId, $array['id']);
        $this->assertEquals($projectName, $array['name']);
    }

    private function createProjectWithId(int $id) : Project
    {
        $project = new Project();
        $this->setId($project, $id);
        return $project;
    }

    private function map(Project $project) : array
    {
        $mapper = new ProjectMapper();
        return $mapper->mapEntity($project);
    }
}
