<?php

namespace Divante\Bundle\AdventureBundle\Events\Hardware;

use Divante\Bundle\AdventureBundle\Entity\Hardware\HardwareAgreement;
use \Symfony\Component\EventDispatcher\Event;

class HardwareSignRequestEvent extends Event
{
    private HardwareAgreement $agreement;
    private string $password;
    private string $email;

    public function __construct(HardwareAgreement $agreement, string $password, string $email)
    {
        $this->agreement = $agreement;
        $this->password = $password;
        $this->email = $email;
    }

    public function getAgreement(): HardwareAgreement
    {
        return $this->agreement;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
