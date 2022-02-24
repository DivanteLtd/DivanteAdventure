<?php

namespace Tests\AdventureBundle\Mappers;

use Divante\Bundle\AdventureBundle\Entity\Project;
use Divante\Bundle\AdventureBundle\Mappers\GitlabProjectMapper;
use Divante\Bundle\AdventureBundle\Mappers\ProjectDetailMapper;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\FoundationTestCase;

class ProjectDetailMapperTest extends FoundationTestCase
{

    public function testMappingData() : void
    {
        $projectId = rand(0, 1000000);
        $projectName = "RandomName".rand(0, 1000000);
        $projectType = rand(0, 2);
        $projectSumType = rand(0, 1);
        $projectDescription = "RandomDescription".rand(0, 1000000);
        $projectPhoto = "RandomPhoto".rand(0, 1000000);
        $projectUrl = "RandomUrl".rand(0, 1000000);
        $plannedBudget = rand(0, 1000000);
        $isBillable = rand(0, 1) === 1;
        $code = "RandomCode".rand(0, 1000000);
        $project = $this->createProjectWithId($projectId)
            ->setName($projectName)
            ->setType($projectType)
            ->setSumType($projectSumType)
            ->setDescription($projectDescription)
            ->setPhoto($projectPhoto)
            ->setUrl($projectUrl)
            ->setPlannedBudget($plannedBudget)
            ->setBillable($isBillable)
            ->setCode($code)
            ->setStartedAt(null)
            ->setEndedAt(null);

        $array = $this->map($project);

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('description', $array);
        $this->assertArrayHasKey('photo', $array);
        $this->assertArrayHasKey('url', $array);
        $this->assertArrayHasKey('project_type', $array);
        $this->assertArrayHasKey('sum_type', $array);
        $this->assertArrayHasKey('planned_budget', $array);
        $this->assertArrayHasKey('tribes', $array);
        $this->assertArrayHasKey('criteria', $array);
        $this->assertArrayHasKey('billable', $array);
        $this->assertArrayHasKey('code', $array);

        $this->assertEquals($projectId, $array['id']);
        $this->assertEquals($projectName, $array['name']);
        $this->assertEquals($projectDescription, $array['description']);
        $this->assertEquals($projectPhoto, $array['photo']);
        $this->assertEquals($projectUrl, $array['url']);
        $this->assertEquals($projectType, $array['project_type']);
        $this->assertEquals($projectSumType, $array['sum_type']);
        $this->assertEquals($plannedBudget, $array['planned_budget']);
        $this->assertEquals([], $array['tribes']);
        $this->assertEquals([], $array['criteria']);
        $this->assertEquals($isBillable, $array['billable']);
        $this->assertEquals($code, $array['code']);

    }

    private function createProjectWithId(int $id) : Project
    {
        $project = new Project();
        $this->setId($project, $id);
        return $project;
    }

    private function map(Project $project) : array
    {
        /** @var ObjectRepository|MockObject $repo */
        $repo = $this->getMockBuilder(ObjectRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['findAll', 'findBy'])
            ->getMockForAbstractClass();
        $repo->expects($this->any())->method('findAll')->willReturn([]);
        $repo->expects($this->any())->method('findBy')->willReturn([]);


        /** @var EntityManagerInterface|MockObject $em */
        $em = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRepository'])
            ->getMockForAbstractClass();
        $em->expects($this->any())->method('getRepository')->willReturn($repo);

        $mapper = new ProjectDetailMapper($em, new GitlabProjectMapper());
        return $mapper->mapEntity($project);
    }
}
