<?php

namespace Tests\Entrypoints\Api\News;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\News;
use Divante\Bundle\AdventureBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class EditActionTest extends AbstractNewsTest
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
        $newTitle = "RandomNewTitle".rand(0, 10000);
        $data = [
            'title' => $newTitle,
            'desc' => "RandomDescription".rand(0, 10000),
            'type' => News::TYPE_MARKDOWN,
        ];
        $this->assertNotEquals($newTitle, $this->news->getTitle());

        $url = '/api/news/'.$this->news->getId();
        $response = $this->request('PATCH', $url, $this->user, $data);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->em->refresh($this->news);
        $this->assertEquals($newTitle, $this->news->getTitle());
    }
}