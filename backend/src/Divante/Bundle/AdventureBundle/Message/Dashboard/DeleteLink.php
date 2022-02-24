<?php

namespace Divante\Bundle\AdventureBundle\Message\Dashboard;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class DeleteLink
{
    use ObjectTrait;

    protected int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
