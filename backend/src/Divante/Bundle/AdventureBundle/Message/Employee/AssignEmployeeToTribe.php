<?php
/**
 * Created by PhpStorm.
 * User: pawel
 * Date: 05.04.19
 * Time: 11:42
 */

namespace Divante\Bundle\AdventureBundle\Message\Employee;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class AssignEmployeeToTribe
{
    use ObjectTrait;

    protected int $userId;
    protected int $tribeId;

    public function __construct(int $userId, int $tribeId)
    {
        $this->userId = $userId;
        $this->tribeId = $tribeId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getTribeId(): int
    {
        return $this->tribeId;
    }
}
