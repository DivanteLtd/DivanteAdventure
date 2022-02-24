<?php

namespace Divante\Bundle\AdventureBundle\Infrastructure\Messenger;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBus;

class MessageBuss extends MessageBus
{
    /** @var LoggerInterface */
    protected $logger;

    public function __construct(LoggerInterface $logger, iterable $middlewareHandlers = [])
    {
        $this->logger = $logger;
        parent::__construct($middlewareHandlers);
    }

    /** @inheritDoc */
    public function dispatch($message, array $stamps = []): Envelope
    {
        $result = parent::dispatch($message, $stamps);
        $this->logger->info((string)$message);
        return $result;
    }
}
