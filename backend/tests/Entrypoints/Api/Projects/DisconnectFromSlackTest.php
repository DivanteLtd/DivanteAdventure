<?php

namespace Tests\Entrypoints\Api\Projects;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Project;
use Divante\Bundle\AdventureBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class DisconnectFromSlackTest extends AbstractProjectsTest
{
    private ?Project $project = null;
    private ?User $user = null;
    private ?Employee $employee = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->project = $this->generateProject();
        $this->project->setSlackStatus(Project::SLACK_AUTHORIZED);
        $this->user = $this->generateFosUser(['ROLE_MANAGER']);
        $this->employee = $this->generateEmployee($this->user);
        $this->em->flush();
    }

    protected function tearDown(): void
    {
        $this->em->remove($this->employee);
        $this->em->remove($this->user);
        $this->em->remove($this->project);
        $this->em->flush();
        parent::tearDown();
    }

    public function testEntrypoint() : void
    {
        $this->assertNotEquals(Project::SLACK_STATUS_NOT_ASKED, $this->project->getSlackStatus());
        $url = sprintf("/api/projects/%s/disconnectSlack", $this->project->getId());
        $response = $this->request('POST', $url, $this->user);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        /** @var array<int,mixed> $json */
        $json = json_decode($response->getContent(), true);
        $this->assertEmpty($json);

        $this->em->refresh($this->project);
        $this->assertEquals(Project::SLACK_STATUS_NOT_ASKED, $this->project->getSlackStatus());
    }
}