<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\Message\Dashboard;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class UpdateNotification
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
