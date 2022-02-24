<?php

namespace Tests\Entrypoints\Api\Tribe;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\LevelingStrategy;
use Divante\Bundle\AdventureBundle\Entity\Position;
use Divante\Bundle\AdventureBundle\Entity\Tribe;
use Divante\Bundle\AdventureBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Tests\Entrypoints\AbstractEntrypointTest;

class DetachPositionActionTest extends AbstractEntrypointTest
{
    private ?Tribe $tribe = null;
    private ?Position $position = null;
    private ?User $user = null;
    private ?Employee $employee = null;
    private ?LevelingStrategy $strategy = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tribe = $this->generateTribe();
        $this->user = $this->generateFosUser(['ROLE_TRIBE_MASTER']);
        $this->employee = $this->generateEmployee($this->user);
        $this->strategy = (new LevelingStrategy())
            ->setName("RandomName".rand(0, 10000))
            ->setCreatedAt()
            ->setUpdatedAt();
        $this->em->persist($this->strategy);
        $this->position = (new Position())
            ->setName("RandomName".rand(0, 10000))
            ->setStrategy($this->strategy)
            ->setCreatedAt()
            ->setUpdatedAt();
        $this->position->getTribes()->add($this->tribe);
        $this->em->persist($this->position);
        $this->em->flush();
    }

    protected function tearDown(): void
    {
        $this->em->remove($this->position);
        $this->em->remove($this->strategy);
        $this->em->remove($this->tribe);
        $this->em->remove($this->user);
        $this->em->remove($this->employee);
        $this->em->flush();
        parent::tearDown();
    }

    public function testEntrypoint() : void
    {
        $url = sprintf('/api/tribe/%s/position/%s', $this->tribe->getId(), $this->position->getId());
        $response = $this->request('DELETE', $url, $this->user);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $json = json_decode($response->getContent(), true);
        $this->assertEmpty($json);
        $this->em->refresh($this->tribe);
        $this->assertNotContains($this->position, $this->tribe->getPositions());
    }
}