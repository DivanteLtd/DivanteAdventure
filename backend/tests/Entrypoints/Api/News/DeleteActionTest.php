<?php

namespace Tests\Entrypoints\Api\News;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\News;
use Divante\Bundle\AdventureBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class DeleteActionTest extends AbstractNewsTest
{
    private ?News $news = null;
    private ?User $user = null;
    private ?Employee $employee = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->generateFosUser(['ROLE_HR']);
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
        $response = $this->request('DELETE', '/api/news/'.$this->news->getId(), $this->user);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }
}