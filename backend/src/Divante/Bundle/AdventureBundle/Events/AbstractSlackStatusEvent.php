<?php

namespace Divante\Bundle\AdventureBundle\Events;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Symfony\Component\EventDispatcher\Event;

abstract class AbstractSlackStatusEvent extends Event
{
    abstract public function isRequestContainingToday() : bool;
    abstract public function isEventAcceptingRequest() : bool;
    abstract public function getTribeNotificationType() : ?int;
    abstract public function getEmployee() : Employee;
}
