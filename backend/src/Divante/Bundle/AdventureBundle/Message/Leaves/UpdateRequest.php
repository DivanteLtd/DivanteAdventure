<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 13.02.19
 * Time: 15:55
 */

namespace Divante\Bundle\AdventureBundle\Message\Leaves;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\LeaveRequest;
use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;
use Divante\Bundle\AdventureBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;

class UpdateRequest
{
    use ObjectTrait;

    private int $requestId;
    private ?array $days;
    private ?int $status;
    private ?string $comment;
    private ?User $user;

    /**
     * UpdateRequest constructor.
     * @param int $requestId
     * @param array|null $days
     * @param int|null $status
     * @param string|null $comment
     * @param User|null $user
     * @throws \Exception
     */
    public function __construct(
        int $requestId,
        ?array $days,
        ?int $status,
        ?string $comment,
        ?User $user
    ) {
        $this->days = $days;
        $this->requestId = $requestId;
        $this->status = $status;
        $this->comment = $comment;
        $this->user = $user;
    }

    public function getRequestId() : int
    {
        return $this->requestId;
    }

    public function getDays() : ?array
    {
        return $this->days;
    }

    public function getStatus() : ?int
    {
        return $this->status;
    }

    public function getComment() : ?string
    {
        return $this->comment;
    }

    public function getUser() : ?User
    {
        return $this->user;
    }
}
