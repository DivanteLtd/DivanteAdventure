<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 * Date: 20.05.19
 * Time: 09:39
 */

namespace Divante\Bundle\AdventureBundle\Message\Dashboard;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class DeleteNotification
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
