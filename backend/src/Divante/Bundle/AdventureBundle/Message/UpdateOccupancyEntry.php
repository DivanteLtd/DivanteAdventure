<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 04.01.19
 * Time: 10:03
 */

namespace Divante\Bundle\AdventureBundle\Message;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\EmployeeOccupancy;
use Divante\Bundle\AdventureBundle\Entity\Project;
use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpdateOccupancyEntry
{
    use ObjectTrait;

    private EmployeeOccupancy $entry;
    private ?Employee $employee;
    private ?Project $project;
    private ?int $occupancy;
    private ?int $timestamp;

    public function __construct(
        ?EmployeeOccupancy $entry,
        ?Employee $employee,
        ?Project $project,
        ?int $occupancy,
        ?int $timestamp
    ) {
        if (is_null($entry)) {
            throw new NotFoundHttpException("Entry not found.");
        }
        $this->entry = $entry;
        $this->employee = $employee;
        $this->project = $project;
        $this->occupancy = $occupancy;
        $this->timestamp = $timestamp;
    }

    public function getEntry(): EmployeeOccupancy
    {
        return $this->entry;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function getOccupancy(): ?int
    {
        return $this->occupancy;
    }

    public function getTimestamp(): ?int
    {
        return $this->timestamp;
    }
}
