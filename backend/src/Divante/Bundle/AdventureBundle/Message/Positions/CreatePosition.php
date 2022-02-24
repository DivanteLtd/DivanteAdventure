<?php

namespace Divante\Bundle\AdventureBundle\Message\Positions;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class CreatePosition
{
    use ObjectTrait;

    private string $name;
    private int $strategyId;

    public function __construct(string $name, int $strategyId)
    {
        $this->name = $name;
        $this->strategyId = $strategyId;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getStrategyId() : int
    {
        return $this->strategyId;
    }
}
