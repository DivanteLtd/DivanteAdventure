<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 * Date: 04.01.19
 * Time: 09:39
 */

namespace Divante\Bundle\AdventureBundle\Message;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class DeleteTribe
{
    use ObjectTrait;

    private int $entryId;

    public function __construct(int $id)
    {
        $this->entryId = $id;
    }

    public function getEntryId() : int
    {
        return $this->entryId;
    }
}
