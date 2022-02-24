<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 04.01.19
 * Time: 08:29
 */

namespace Divante\Bundle\AdventureBundle\Message;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class CreateOccupancyEntry
{
    use ObjectTrait;

    private int $employeeId;
    private int $projectId;
    private int $occupancy;
    private int $timestamp;

    public function __construct(int $employeeId, int $projectId, int $occupancy, int $timestamp)
    {
        $this->employeeId = $employeeId;
        $this->projectId = $projectId;
        $this->occupancy = $occupancy;
        $this->timestamp = $timestamp;
    }

    public function getEmployeeId(): int
    {
        return $this->employeeId;
    }

    public function getProjectId(): int
    {
        return $this->projectId;
    }

    public function getOccupancy(): int
    {
        return $this->occupancy;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }
}
