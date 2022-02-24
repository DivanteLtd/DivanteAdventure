<?php

namespace Divante\Bundle\AdventureBundle\Message\Positions;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class DeletePosition
{
    use ObjectTrait;

    private int $positonId;

    public function __construct(int $positiongId)
    {
        $this->positonId = $positiongId;
    }

    public function getPositionId() : int
    {
        return $this->positonId;
    }
}
