<?php

namespace Tests\Entrypoints\Api\Projects;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Project;
use Divante\Bundle\AdventureBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class ListWithDetailsActionTest extends AbstractProjectsTest
{
    private ?Project $project = null;
    private ?User $user = null;
    private ?Employee $employee = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->project = $this->generateProject();
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
        $response = $this->request('GET', '/api/projects/details', $this->user);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        /** @var array<int,array<string,mixed>> $json */
        $json = json_decode($response->getContent(), true);
        $this->assertGreaterThanOrEqual(1, count($json));
        /** @var array<int,array<string,mixed>> $filteredJson */
        $filteredJson = array_filter($json, fn(array $entry) : bool => $entry['id'] === $this->project->getId());
        $this->assertCount(1, $filteredJson);
        /** @var array<string,mixed> $entry */
        $entry = array_pop($filteredJson);
        $this->assertArrayHasKey('name', $entry);
        $this->assertEquals($this->project->getName(), $entry['name']);
    }
}