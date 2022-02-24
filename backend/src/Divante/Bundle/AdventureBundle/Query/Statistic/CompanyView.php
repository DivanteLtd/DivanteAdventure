<?php

namespace Divante\Bundle\AdventureBundle\Query\Statistic;

class CompanyView
{
    protected int $possibleSeconds;
    protected int $planedSeconds;
    protected int $billableSeconds;
    protected int $noBillableSeconds;

    public function __construct(int $possibleSeconds, int $planedSeconds, int $billableSeconds, int $noBillableSeconds)
    {
        $this->possibleSeconds   = $possibleSeconds;
        $this->planedSeconds     = $planedSeconds;
        $this->billableSeconds   = $billableSeconds;
        $this->noBillableSeconds = $noBillableSeconds;
    }

    public function getPossibleSeconds(): int
    {
        return $this->possibleSeconds;
    }

    public function getPlanedSeconds(): int
    {
        return $this->planedSeconds;
    }

    public function getBillableSeconds(): int
    {
        return $this->billableSeconds;
    }

    public function getNoBillableSeconds(): int
    {
        return $this->noBillableSeconds;
    }
}
