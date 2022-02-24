<?php

namespace Divante\Bundle\AdventureBundle\Message;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class SendEmail
{
    use ObjectTrait;

    protected int $projectId;

    public function __construct(int $projectId)
    {
        $this->projectId = $projectId;
    }

    public function getProjectId(): int
    {
        return $this->projectId;
    }
}
