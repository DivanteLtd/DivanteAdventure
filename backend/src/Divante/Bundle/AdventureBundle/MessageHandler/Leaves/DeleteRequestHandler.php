<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 14.02.19
 * Time: 11:54
 */

namespace Divante\Bundle\AdventureBundle\MessageHandler\Leaves;

use Divante\Bundle\AdventureBundle\Entity\LeaveRequest;
use Divante\Bundle\AdventureBundle\Message\Leaves\DeleteRequest;
use Doctrine\ORM\EntityManagerInterface;

class DeleteRequestHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param DeleteRequest $message
     * @throws \Exception
     */
    public function __invoke(DeleteRequest $message) : void
    {
        $em = $this->em;
        try {
            $request = $message->getLeaveRequest();
            $request
                ->setHidden(true)
                ->setStatus(LeaveRequest::REQUEST_STATUS_RESIGNED);
            $em->flush();
        } catch (\Exception $e) {
            throw new \Exception("Deleting leave request failed", 0, $e);
        }
    }
}
