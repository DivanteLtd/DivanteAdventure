<?php

namespace Divante\Bundle\AdventureBundle\Infrastructure\Mercure;

use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;

class MercureUpdate
{
    private PublisherInterface $publisher;

    public function __construct(PublisherInterface $publisher)
    {
        $this->publisher = $publisher;
    }

    /**
     * @param array<mixed,mixed> $jsonData
     * @param string $id
     * @param string $action
     */
    public function sendUpdate(array $jsonData, string $id, string $action = 'message'): void
    {
        $fullJsonData = [
            '@data' => $jsonData,
            '@action' => $action,
        ];
        $update = new Update($id, json_encode($fullJsonData), true);
        ($this->publisher)($update);
    }
}
