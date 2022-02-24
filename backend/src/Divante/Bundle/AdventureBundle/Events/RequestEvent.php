<?php

namespace Divante\Bundle\AdventureBundle\Events;

use Divante\Bundle\AdventureBundle\Entity\LeaveRequest;
use Symfony\Component\EventDispatcher\Event;

class RequestEvent extends Event
{
    private LeaveRequest $leaveRequest;

    public function __construct(LeaveRequest $leaveRequest)
    {
        $this->leaveRequest = $leaveRequest;
    }

    public function getRequest() : LeaveRequest
    {
        return $this->leaveRequest;
    }
}
