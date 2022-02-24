<?php

namespace Divante\Bundle\AdventureBundle\Events;

use Symfony\Component\EventDispatcher\Event;

class SlackAdminLogEvent extends Event
{
    private string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
