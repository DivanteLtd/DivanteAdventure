<?php
/**
 * Created by PhpStorm.
 * User: pawel
 * Date: 16.01.19
 * Time: 14:15
 */

namespace Divante\Bundle\AdventureBundle\Query\Pairings;

class PairingsView
{
    protected int  $id;
    protected int $employeeId;
    protected int $projectId;
    protected int $secondsPerDay;
    protected int $timestamp;
    protected ?string $projectDateEndTime;
    protected bool $visible;

    public function __construct(
        int $id,
        int $employeeId,
        int $projectId,
        int $secondsPerDay,
        int $timestamp,
        ?string $projectDateEndTime,
        ?bool $visible
    ) {
        $this->id = $id;
        $this->employeeId = $employeeId;
        $this->projectId = $projectId;
        $this->secondsPerDay = $secondsPerDay;
        $this->timestamp = $timestamp;
        $this->projectDateEndTime = $projectDateEndTime;
        $this->visible = !$visible;
        if ($visible == null) {
            $this->visible = true;
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmployeeId(): int
    {
        return $this->employeeId;
    }

    public function getProjectId(): int
    {
        return $this->projectId;
    }

    public function getSecondsPerDay(): int
    {
        return $this->secondsPerDay;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getProjectDateEndTime(): ?string
    {
        return $this->projectDateEndTime;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }
}
