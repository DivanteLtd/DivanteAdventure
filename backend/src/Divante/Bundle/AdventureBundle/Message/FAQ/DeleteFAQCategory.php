<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\Message\FAQ;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class DeleteFAQCategory
{
    use ObjectTrait;
    private int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function getId() : int
    {
        return $this->id;
    }
}
