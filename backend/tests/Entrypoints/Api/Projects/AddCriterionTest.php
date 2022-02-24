<?php

namespace Tests\Entrypoints\Api\Projects;

use Divante\Bundle\AdventureBundle\Entity\DataProcessingCriteria;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Project;
use Divante\Bundle\AdventureBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class AddCriterionTest extends AbstractProjectsTest
{
    private ?Project $project = null;
    private ?User $user = null;
    private ?Employee $employee = null;
    private ?DataProcessingCriteria $criterion = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->project = $this->generateProject();
        $this->user = $this->generateFosUser(['ROLE_MANAGER']);
        $this->employee = $this->generateEmployee($this->user);
        $this->criterion = new DataProcessingCriteria();
        $this->criterion->setNamePl("RandomName".rand(0, 10000))
            ->setNameEn("RandomName".rand(0, 10000));
        $this->em->persist($this->criterion);
        $this->em->flush();
    }

    protected function tearDown(): void
    {
        $this->em->remove($this->criterion);
        $this->em->remove($this->employee);
        $this->em->remove($this->user);
        $this->em->remove($this->project);
        $this->em->flush();
        parent::tearDown();
    }

    public function testEntrypoint() : void
    {
        $url = sprintf("/api/projects/%s/criterium", $this->project->getId());
        $response = $this->request('POST', $url, $this->user, [ 'criterionId' => $this->criterion->getId() ]);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        /** @var array<int,mixed> $json */
        $json = json_decode($response->getContent(), true);
        $this->assertEmpty($json);

        $this->em->refresh($this->project);
        $this->assertContains($this->criterion, $this->project->getCriteria());
    }
}