<?php

namespace Divante\Bundle\AdventureBundle\Services\Structure;

use ArrayAccess;

abstract class AbstractQueue implements \Countable, ArrayAccess
{
    /** @var array<int,mixed> */
    protected array $elements;

    /**
     * AbstractQueue constructor.
     * @param array<int,mixed> $elements
     */
    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }

    /**
     * @return mixed
     * @throws StructureException
     */
    abstract public function get();

    /**
     * @param mixed $value
     * @return AbstractQueue
     */
    abstract public function put($value) : AbstractQueue;

    /** @return array<int,mixed> */
    public function toArray() : array
    {
        return $this->elements;
    }

    public function count() : int
    {
        return count($this->elements);
    }


    /**
     * @param int $offset
     * @return bool
     */
    public function offsetExists($offset) : bool
    {
        return isset($this->elements[$offset]);
    }

    /**
     * @param int $offset
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return isset($this->elements[$offset]) ? $this->elements[$offset] : null;
    }

    /**
     * @param int|null $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value) : void
    {
        if (is_null($offset)) {
            $this->put($value);
        } else {
            $this->elements[$offset] = $value;
        }
    }

    /**
     * @param int $offset
     */
    public function offsetUnset($offset) : void
    {
        unset($this->elements[$offset]);
    }
}
