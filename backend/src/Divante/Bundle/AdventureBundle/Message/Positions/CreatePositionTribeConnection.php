<?php

namespace Divante\Bundle\AdventureBundle\Message\Positions;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class CreatePositionTribeConnection
{
    use ObjectTrait;

    private int $positionId;
    private int $tribeId;

    public function __construct(int $positionId, int $tribeId)
    {
        $this->positionId = $positionId;
        $this->tribeId = $tribeId;
    }

    public function getPositionId() : int
    {
        return $this->positionId;
    }

    public function getTribeId() : int
    {
        return $this->tribeId;
    }
}
