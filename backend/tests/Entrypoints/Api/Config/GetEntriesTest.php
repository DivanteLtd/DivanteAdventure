<?php

namespace Tests\Entrypoints\Api\Config;

use Divante\Bundle\AdventureBundle\Entity\ConfigEntry;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class GetEntriesTest extends AbstractConfigTest
{
    private ?User $user = null;
    private ?Employee $employee = null;
    private ?ConfigEntry $config = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->generateFosUser(['ROLE_SUPER_ADMIN']);
        $this->employee = $this->generateEmployee($this->user);
        $this->config = $this->generateConfigEntry($this->employee);
        $this->em->flush();
    }

    protected function tearDown(): void
    {
        $this->em->flush();
        parent::tearDown();
    }

    public function testEntrypoint(): void
    {
        $response = $this->request('GET', '/api/config', $this->user);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        /** @var array<string,null|array<string,mixed>> $json */
        $json = json_decode($response->getContent(), true);
        $found = false;
        /** @var array<string,mixed> $entry */
        foreach ($json as $entry) {
            $this->assertArrayHasKey('key', $entry);
            $this->assertArrayHasKey('value', $entry);
            if ($entry['key'] === $this->config->getKey()) {
                $found = true;
            }
        }
        $this->assertTrue($found);
    }
}
