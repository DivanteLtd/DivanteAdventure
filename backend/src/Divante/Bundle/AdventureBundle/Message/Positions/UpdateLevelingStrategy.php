<?php

namespace Divante\Bundle\AdventureBundle\Message\Positions;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class UpdateLevelingStrategy
{
    use ObjectTrait;

    private int $updateId;
    private ?string $name;

    public function __construct(int $updateId, ?string $name)
    {
        $this->updateId = $updateId;
        $this->name = $name;
    }

    public function getUpdateId() : int
    {
        return $this->updateId;
    }

    public function getName() : ?string
    {
        return $this->name;
    }
}
