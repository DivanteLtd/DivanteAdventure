<?php


namespace Divante\Bundle\AdventureBundle\Query\Report;

class OccupancyView
{
    /** @var string */
    private $projectName;
    /** @var int */
    private $occupancy;

    /**
     * OccupancyView constructor.
     * @param string $projectName
     * @param int $occupancy
     */
    public function __construct(string $projectName, int $occupancy)
    {
        $this->projectName = $projectName;
        $this->occupancy = $occupancy;
    }

    /**
     * @return string
     */
    public function getProjectName(): string
    {
        return $this->projectName;
    }

    /**
     * @return int
     */
    public function getOccupancy(): int
    {
        return $this->occupancy;
    }
}
