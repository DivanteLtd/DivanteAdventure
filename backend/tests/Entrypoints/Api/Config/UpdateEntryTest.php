<?php

namespace Tests\Entrypoints\Api\Config;

use Divante\Bundle\AdventureBundle\Entity\ConfigEntry;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class UpdateEntryTest extends AbstractConfigTest
{
    private ?User $user = null;
    private ?Employee $employee = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->generateFosUser(['ROLE_SUPER_ADMIN']);
        $this->employee = $this->generateEmployee($this->user);
        $this->em->flush();
    }

    protected function tearDown(): void
    {
        $this->em->remove($this->employee);
        $this->em->remove($this->user);
        $this->em->flush();
        parent::tearDown();
    }

    public function testCreatingNewKey(): void
    {
        $key = 'random_key_'.rand(0, 10000);
        $value ='random_value_'.rand(0, 10000);
        $data = [ $key => $value ];
        $response = $this->request('POST', '/api/config', $this->user, [ 'entries' => $data ]);
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());

        $repo = $this->em->getRepository('AdventureBundle:ConfigEntry');
        /** @var ConfigEntry[] $entries */
        $entries = $repo->findBy([
            'key' => $key,
        ]);
        $this->assertCount(1, $entries);
        $this->assertSame($value, $entries[0]->getValue());
        $this->em->remove($entries[0]);
        $this->em->flush();
    }

    public function updatingKeyWithoutChangingValue(): void
    {
        $key = 'random_key_'.rand(0, 10000);
        $value ='random_value_'.rand(0, 10000);
        $group ='random_value_'.rand(0, 10000);
        $data = [ $key => $value ];
        $oldEntry = (new ConfigEntry())
            ->setKey($key)
            ->setValue($value)
            ->setGroup($group)
            ->setResponsible($this->employee)
            ->setCreatedAt();
        $this->em->persist($oldEntry);
        $this->em->flush();

        $response = $this->request('POST', '/api/config', $this->user, [ 'entries' => $data ]);
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());

        $repo = $this->em->getRepository('AdventureBundle:ConfigEntry');
        /** @var ConfigEntry[] $entries */
        $entries = $repo->findBy([
            'key' => $key,
        ]);

        $this->em->refresh($oldEntry);
        $this->assertCount(1, $entries);
        $this->assertSame($value, $entries[0]->getValue());
        $this->assertSame($oldEntry->getCreatedAt(), $entries[0]->getCreatedAt());
        $this->assertNull($oldEntry->getReplacedAt());
        $this->em->remove($entries[0]);
        $this->em->flush();
    }

    public function updatingKeyWithChangingValue(): void
    {
        $key = 'random_key_'.rand(0, 10000);
        $oldValue = 'random_old_value_'.rand(0, 10000);
        $newValue = 'random_new_value_'.rand(0, 10000);
        $group= 'group_'.rand(0, 10000);
        $data = [ $key => $newValue ];
        $oldEntry = (new ConfigEntry())
            ->setKey($key)
            ->setValue($oldValue)
            ->setGroup($group)
            ->setResponsible($this->employee)
            ->setCreatedAt();
        $this->em->persist($oldEntry);
        $this->em->flush();

        $response = $this->request('POST', '/api/config', $this->user, [ 'entries' => $data ]);
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());

        $repo = $this->em->getRepository('AdventureBundle:ConfigEntry');
        /** @var ConfigEntry[] $entries */
        $entries = $repo->findBy([
            'key' => $key,
        ]);
        $this->em->refresh($oldEntry);
        $this->assertCount(2, $entries);
        $this->assertNotNull($oldEntry->getReplacedAt());
        foreach($entries as $entry) {
            $this->em->remove($entry);
        }
        $this->em->flush();
    }
}
