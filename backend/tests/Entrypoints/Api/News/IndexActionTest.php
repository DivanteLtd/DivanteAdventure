<?php

namespace Tests\Entrypoints\Api\News;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\News;
use Divante\Bundle\AdventureBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class IndexActionTest extends AbstractNewsTest
{
    private ?News $news = null;
    private ?User $user = null;
    private ?Employee $employee = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->generateFosUser(['ROLE_USER']);
        $this->employee = $this->generateEmployee($this->user);
        $this->news = $this->generateNews($this->employee);
        $this->em->flush();
    }

    protected function tearDown(): void
    {
        $this->em->remove($this->news);
        $this->em->remove($this->employee);
        $this->em->remove($this->user);
        $this->em->flush();
        parent::tearDown();
    }

    public function testEntrypoint() : void
    {
        $response = $this->request('GET', '/api/news', $this->user);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        /** @var array<int,array<string,mixed>> $json */
        $json = json_decode($response->getContent(), true);
        $this->assertGreaterThanOrEqual(1, count($json));

        /** @var array<int,array<string,mixed>> $entries */
        $entries = array_filter($json, fn(array $entry) => $entry['id'] === $this->news->getId());
        $this->assertCount(1, $entries);

        /** @var array<string,mixed> $entry */
        $entry = array_pop($entries);
        $this->assertEquals($this->news->getTitle(), $entry['title']);
    }
}