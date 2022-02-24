<?php

namespace Tests\Entrypoints\Api\Tribe;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Tribe;
use Divante\Bundle\AdventureBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Tests\Entrypoints\AbstractEntrypointTest;

class NewActionTest extends AbstractEntrypointTest
{
    private ?Tribe $tribe = null;
    private ?User $user = null;
    private ?Employee $employee = null;

    protected function setUp(): void
    {
        parent::setUp();
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
        $name = "RandomGeneratedName".rand(0, 10000);
        $response = $this->request('POST', '/api/tribe', $this->user, [
            'name' => $name,
        ]);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $json = json_decode($response->getContent(), true);
        $this->assertEmpty($json);

        $this->tribe = $this->em->getRepository('AdventureBundle:Tribe')->findOneBy([
            'name' => $name
        ]);
        $this->assertNotNull($this->tribe);
    }
}