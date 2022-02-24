<?php


namespace Divante\Bundle\AdventureBundle\Message\Dictionaries\Contract;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class DeleteContractType
{
    use ObjectTrait;

    private int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
