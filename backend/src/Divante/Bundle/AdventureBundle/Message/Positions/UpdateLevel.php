<?php

namespace Divante\Bundle\AdventureBundle\Message\Positions;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class UpdateLevel
{
    use ObjectTrait;

    private int $updatedLevelId;
    private ?string $newName;
    private ?int $newStrategyId;
    private ?int $priority;

    public function __construct(int $levelId, ?string $name, ?int $strategyId, ?int $priority)
    {
        $this->updatedLevelId = $levelId;
        $this->newName = $name;
        $this->newStrategyId = $strategyId;
        $this->priority = $priority;
    }

    public function getUpdatedLevelId() : int
    {
        return $this->updatedLevelId;
    }

    public function getName() : ?string
    {
        return $this->newName;
    }

    public function getStrategyId() : ?int
    {
        return $this->newStrategyId;
    }

    public function getPriority() : ?int
    {
        return $this->priority;
    }
}
