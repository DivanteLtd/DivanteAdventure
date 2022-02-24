<?php

namespace Divante\Bundle\AdventureBundle\Services\Slack;

interface SlackSendableMessage
{
    /**
     * @return array<string|int,mixed>
     */
    public function toJson() : array;
}
