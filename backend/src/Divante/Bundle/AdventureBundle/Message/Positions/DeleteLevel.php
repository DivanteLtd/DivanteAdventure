<?php

namespace Divante\Bundle\AdventureBundle\Message\Positions;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class DeleteLevel
{
    use ObjectTrait;

    private int $deletedLevelId;

    public function __construct(int $deletedLevelId)
    {
        $this->deletedLevelId = $deletedLevelId;
    }

    public function getDeletedLevelId() : int
    {
        return $this->deletedLevelId;
    }
}
