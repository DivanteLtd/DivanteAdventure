<?php

namespace Tests\Entrypoints\Api\Tribe;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Tribe;
use Divante\Bundle\AdventureBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Tests\Entrypoints\AbstractEntrypointTest;

class IndexActionTest extends AbstractEntrypointTest
{
    private ?Tribe $tribe = null;
    private ?User $user = null;
    private ?Employee $employee = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->generateFosUser(['ROLE_USER']);
        $this->employee = $this->generateEmployee($this->user);
        $this->tribe = $this->generateTribe();
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
        $response = $this->request('GET', '/api/tribe', $this->user);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        /** @var array<int,array<string,mixed>> $json */
        $json = json_decode($response->getContent(), true);
        $this->assertGreaterThanOrEqual(1, count($json));
        /** @var array<int,array<string,mixed>> $filteredJson */
        $filteredJson = array_filter($json, fn(array $entry) : bool => $entry['id'] === $this->tribe->getId());
        $this->assertCount(1, $filteredJson);
        /** @var array<string,mixed> $entry */
        $entry = array_pop($filteredJson);
        $this->assertArrayHasKey('name', $entry);
        $this->assertEquals($this->tribe->getName(), $entry['name']);
    }
}