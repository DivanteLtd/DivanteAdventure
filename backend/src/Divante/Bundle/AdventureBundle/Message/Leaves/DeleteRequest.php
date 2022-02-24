<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 14.02.19
 * Time: 11:41
 */

namespace Divante\Bundle\AdventureBundle\Message\Leaves;

use Divante\Bundle\AdventureBundle\Entity\LeaveRequest;
use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;
use Doctrine\ORM\EntityManagerInterface;

class DeleteRequest
{
    use ObjectTrait;

    private LeaveRequest $request;

    public function __construct(int $requestId, EntityManagerInterface $em)
    {
        /** @var LeaveRequest|null $leaveRequest */
        $leaveRequest = $em->getRepository(LeaveRequest::class)->find($requestId);
        if (is_null($leaveRequest)) {
            throw new \Exception("Request with given ID $requestId does not exists.");
        }
        $this->request = $leaveRequest;
    }

    public function getLeaveRequest() : LeaveRequest
    {
        return $this->request;
    }
}
