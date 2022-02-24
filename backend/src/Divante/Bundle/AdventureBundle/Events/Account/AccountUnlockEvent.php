<?php

namespace Divante\Bundle\AdventureBundle\Events\Account;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class AccountUnlockEvent
 *
 * @package Divante\Bundle\AdventureBundle\Events\Account
 * @author PK <pk@divante.com>
 */
class AccountUnlockEvent extends Event
{
    private Employee $employee;


    public function __construct(Employee $employee)
    {
        $this->employee = $employee;
    }

    public function getEmployee(): Employee
    {
        return $this->employee;
    }
}
