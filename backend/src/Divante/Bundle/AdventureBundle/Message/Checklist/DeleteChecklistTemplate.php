<?php

namespace Divante\Bundle\AdventureBundle\Message\Checklist;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class DeleteChecklistTemplate
{
    use ObjectTrait;

    private int $id;

    public function __construct(int $checklistId)
    {
        $this->id = $checklistId;
    }

    public function getChecklistId() : int
    {
        return $this->id;
    }
}
