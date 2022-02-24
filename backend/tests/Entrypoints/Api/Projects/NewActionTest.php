<?php

namespace Tests\Entrypoints\Api\Projects;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Project;
use Divante\Bundle\AdventureBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class NewActionTest extends AbstractProjectsTest
{
    private ?Project $project = null;
    private ?User $user = null;
    private ?Employee $employee = null;

    protected function setUp(): void
    {
        parent::setUp();
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
        $randomName = "RandomName".rand(0, 10000);
        $response = $this->request('POST', '/api/projects', $this->user, [
            'name' => $randomName,
            'code' => "randomCode".rand(0, 10000),
            'billable' => false,
        ]);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        /** @var array<int,mixed> $json */
        $json = json_decode($response->getContent(), true);
        $this->assertEmpty($json);

        $this->project = $this->em->getRepository('AdventureBundle:Project')->findOneBy([
            'name' => $randomName,
        ]);
        $this->assertNotNull($this->project);
    }
}