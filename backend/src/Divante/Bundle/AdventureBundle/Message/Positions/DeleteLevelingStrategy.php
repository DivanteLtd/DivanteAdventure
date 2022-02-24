<?php

namespace Divante\Bundle\AdventureBundle\Message\Positions;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class DeleteLevelingStrategy
{
    use ObjectTrait;

    private int $id;

    public function __construct(int $deleteId)
    {
        $this->id = $deleteId;
    }

    public function getDeleteId() : int
    {
        return $this->id;
    }
}
