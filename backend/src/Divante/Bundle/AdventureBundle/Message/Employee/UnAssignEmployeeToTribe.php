<?php
/**
 * Created by PhpStorm.
 * User: pawel
 * Date: 05.04.19
 * Time: 11:42
 */

namespace Divante\Bundle\AdventureBundle\Message\Employee;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class UnAssignEmployeeToTribe
{
    use ObjectTrait;

    /** @var integer */
    protected $userId;
    /** @var int */
    protected $tribeId;

    /**
     * UnAssignEmployeeToTribe constructor.
     * @param int $userId
     * @param int $tribeId
     */
    public function __construct(int $userId, int $tribeId)
    {
        $this->userId = $userId;
        $this->tribeId = $tribeId;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return int
     */
    public function getTribeId(): int
    {
        return $this->tribeId;
    }
}
