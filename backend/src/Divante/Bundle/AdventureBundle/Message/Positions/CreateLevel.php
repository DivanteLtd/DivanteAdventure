<?php

namespace Divante\Bundle\AdventureBundle\Message\Positions;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class CreateLevel
{
    use ObjectTrait;

    private string $name;
    private int $strategyId;
    private int $priority;

    public function __construct(string $name, int $strategyId, int $priority)
    {
        $this->name = $name;
        $this->strategyId = $strategyId;
        $this->priority = $priority;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getStrategyId() : int
    {
        return $this->strategyId;
    }

    public function getPriority() : int
    {
        return $this->priority;
    }
}
