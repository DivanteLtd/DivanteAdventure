<?php

namespace Tests\AdventureBundle\Mappers;

use Divante\Bundle\AdventureBundle\Entity\GitlabProject;
use Divante\Bundle\AdventureBundle\Mappers\GitlabProjectMapper;
use Divante\Bundle\AdventureBundle\Mappers\Mapper;
use Tests\FoundationTestCase;

class GitlabProjectMapperTest extends FoundationTestCase
{
    public function testMappingData() : void
    {
        $projectName = "RandomProjectName".rand(0, 1000000);
        $projectId = rand(0, 1000000);
        $gitlabId = rand(0, 1000000);
        $type = rand(0, 1000000);

        $project = $this->createProjectWithId($projectId)
            ->setUpdatedAt()
            ->setCreatedAt()
            ->setName($projectName)
            ->setGitlabType($type)
            ->setGitlabId($gitlabId);
        $array = $this->map($project);

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('type', $array);

        $this->assertEquals($projectId, $array['id']);
        $this->assertEquals($projectName, $array['name']);
        $this->assertEquals($type, $array['type']);
    }

    private function createProjectWithId(int $id) : GitlabProject
    {
        $project = new GitlabProject();
        $this->setId($project, $id);
        return $project;
    }

    private function map(GitlabProject $project) : array
    {
        return (new GitlabProjectMapper())->mapEntity($project);
    }
}
