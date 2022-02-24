<?php

namespace Tests\Entrypoints\Api\Tribe;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Tribe;
use Divante\Bundle\AdventureBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Tests\Entrypoints\AbstractEntrypointTest;

class DeleteActionTest extends AbstractEntrypointTest
{
    private ?Tribe $tribe = null;
    private ?User $user = null;
    private ?Employee $employee = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tribe = $this->generateTribe();
        $this->user = $this->generateFosUser(['ROLE_TRIBE_MASTER']);
        $this->employee = $this->generateEmployee($this->user);
        $this->em->flush();
    }

    protected function tearDown(): void
    {
        $this->em->remove($this->tribe);
        $this->em->remove($this->user);
        $this->em->remove($this->employee);
        $this->em->flush();
        parent::tearDown();
    }

    public function testEntrypoint() : void
    {
        $response = $this->request('DELETE', '/api/tribe/'.$this->tribe->getId(), $this->user);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $json = json_decode($response->getContent(), true);
        $this->assertEmpty($json);
    }
}