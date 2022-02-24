<?php

namespace Tests\Entrypoints\Api\Config;

use Divante\Bundle\AdventureBundle\Entity\ConfigEntry;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class GetHistoryTest extends AbstractConfigTest
{
    private ?User $user = null;
    private ?Employee $employee = null;
    private ?ConfigEntry $config = null;
    private ?ConfigEntry $historicalConfig = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->generateFosUser(['ROLE_SUPER_ADMIN']);
        $this->employee = $this->generateEmployee($this->user);
        $this->config = $this->generateConfigEntry($this->employee);
        $this->historicalConfig = $this->generateConfigEntry($this->employee);
        $this->historicalConfig
            ->setKey($this->config->getKey())
            ->setReplacedAt();
        $this->em->flush();
    }

    protected function tearDown(): void
    {
        $this->em->remove($this->config);
        $this->em->remove($this->historicalConfig);
        $this->em->remove($this->employee);
        $this->em->remove($this->user);
        $this->em->flush();
        parent::tearDown();
    }

    public function testEntrypoint(): void
    {
        $response = $this->request('GET', '/api/config/'.$this->config->getKey(), $this->user);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        /** @var array<string,mixed>[] $json */
        $json = json_decode($response->getContent(), true);
        $this->assertCount(2, $json);
    }
}
