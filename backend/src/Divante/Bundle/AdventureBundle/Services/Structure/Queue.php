<?php

namespace Divante\Bundle\AdventureBundle\Services\Structure;

class Queue extends AbstractQueue
{
    /** @inheritDoc */
    public function get()
    {
        if (count($this) === 0) {
            throw new StructureException("Queue is empty");
        }
        return array_shift($this->elements);
    }

    /** @inheritDoc */
    public function put($value) : self
    {
        array_push($this->elements, $value);
        return $this;
    }
}
