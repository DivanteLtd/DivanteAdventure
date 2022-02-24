<?php

namespace Divante\Bundle\AdventureBundle\Message\Positions;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class UpdatePosition
{
    use ObjectTrait;

    private int $updatedPositionId;
    private ?int $newStrategyId;
    private ?string $newName;

    public function __construct(int $updatedPositionId, ?int $newStrategyId, ?string $newName)
    {
        $this->updatedPositionId = $updatedPositionId;
        $this->newStrategyId = $newStrategyId;
        $this->newName = $newName;
    }

    public function getUpdatedPositionId() : int
    {
        return $this->updatedPositionId;
    }

    public function getNewStrategyId() : ?int
    {
        return $this->newStrategyId;
    }

    public function getNewName() : ?string
    {
        return $this->newName;
    }
}
