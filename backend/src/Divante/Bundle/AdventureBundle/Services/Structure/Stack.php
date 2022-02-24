<?php

namespace Divante\Bundle\AdventureBundle\Services\Structure;

class Stack extends AbstractQueue
{
    /**  @inheritDoc */
    public function get()
    {
        if (count($this) === 0) {
            throw new StructureException("Stack is empty");
        }
        return array_shift($this->elements);
    }

    /** @inheritDoc */
    public function put($value): AbstractQueue
    {
        array_unshift($this->elements, $value);
        return $this;
    }
}
