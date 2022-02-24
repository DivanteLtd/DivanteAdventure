<?php

namespace Divante\Bundle\AdventureBundle\Filters\EmployeeProject;

use Divante\Bundle\AdventureBundle\Entity\EmployeeProject;

class EmployeeProjectActive
{
    public function __invoke(EmployeeProject $pairing) : bool
    {
        $dateFrom = $pairing->getDateFrom();
        $dateTo = $pairing->getDateTo();
        for ($i = 0; $i < min(count($dateFrom), count($dateTo)); $i++) {
            $from = \DateTime::createFromFormat('d-m-Y', '01-'.$dateFrom[$i])->getTimestamp();
            $toString = date('t-m-Y', strtotime('01-'.$dateTo[$i]));
            $to = \DateTime::createFromFormat('d-m-Y', $toString)->getTimestamp();
            $current = time();
            if ($current >= $from && $current <= $to) {
                return true;
            }
        }
        return false;
    }
}
