<?php


namespace Divante\Bundle\AdventureBundle\Query\Report;

class DayView
{
    protected int $timestamp;
    /** @var OccupancyView[] */
    protected array $occupancy;

    public function __construct(int $timestamp)
    {
        $this->timestamp = $timestamp;
    }

    public function addOccupancy(OccupancyView $occupancy) : void
    {
        $this->occupancy[] = $occupancy;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /** @return OccupancyView[] */
    public function getOccupancy(): array
    {
        return $this->occupancy;
    }
}
