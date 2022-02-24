<?php

namespace Tests\Entrypoints\Api\News;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\News;
use Divante\Bundle\AdventureBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class AddActionTest extends AbstractNewsTest
{
    private ?News $news = null;
    private ?User $user = null;
    private ?Employee $employee = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->generateFosUser(['ROLE_HR']);
        $this->employee = $this->generateEmployee($this->user);
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
        $response = $this->request('POST', '/api/news', $this->user, $data);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->news = $this->em->getRepository('AdventureBundle:News')->findOneBy(['title' => $newTitle]);
        $this->assertNotNull($this->news);
    }
}