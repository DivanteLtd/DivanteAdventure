<?php

namespace Divante\Bundle\AdventureBundle\Message\Evidence;

trait EvidenceTrait
{
    private float $hours;
    private int $paidDaysoffHours;
    private int $unpaidDaysoffHours;
    private int $sickLeaveHours;

    public function getHours() : float
    {
        return $this->hours;
    }

    public function getPaidDaysoffHours() : int
    {
        return $this->paidDaysoffHours;
    }

    public function getUnpaidDaysoffHours() : int
    {
        return $this->unpaidDaysoffHours;
    }

    public function getSickLeaveHours() : int
    {
        return $this->sickLeaveHours;
    }
}
