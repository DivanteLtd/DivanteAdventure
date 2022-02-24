<?php

namespace Divante\Bundle\AdventureBundle\Entity;

use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Divante\Bundle\AdventureBundle\Entity\Data\Timestampable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class IntegrationQueue
 * @package Divante\Bundle\AdventureBundle\Entity
 * @ORM\Table(name="integration_queue") @ORM\Entity
 */
class IntegrationQueue
{
    use Id, Timestampable;

    public const TYPE_GITLAB_ADD = 'gitlab_add';
    public const TYPE_GITLAB_REMOVE = 'gitlab_remove';

    public const STATUS_ENQUEUED = 0;
    public const STATUS_DONE = 1;
    public const STATUS_INVALID = 2;

    /**
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Employee")
     * @ORM\JoinColumn(name="employee_id", onDelete="CASCADE", referencedColumnName="id", nullable=false)
     */
    private Employee $employee;

    /**
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Project")
     * @ORM\JoinColumn(name="project_id", onDelete="CASCADE", referencedColumnName="id", nullable=false)
     */
    private Project $project;

    /** @ORM\Column(name="type", type="string") */
    private string $type;

    /** @ORM\Column(name="status", type="integer") */
    private int $status = self::STATUS_ENQUEUED;

    /**
     * @var array<string,mixed>
     * @ORM\Column(name="request_data", type="json", nullable=false)
     */
    private array $requestData = [];

    public function getEmployee() : Employee
    {
        return $this->employee;
    }

    public function setEmployee(Employee $employee) : self
    {
        $this->employee = $employee;
        return $this;
    }

    public function getProject() : Project
    {
        return $this->project;
    }

    public function setProject(Project $project) : self
    {
        $this->project = $project;
        return $this;
    }

    public function getType() : string
    {
        return $this->type;
    }

    public function setType(string $type) : self
    {
        $this->type = $type;
        return $this;
    }

    public function getStatus() : int
    {
        return $this->status;
    }

    public function setStatus(int $status) : self
    {
        $this->status = $status;
        return $this;
    }

    /** @return array<string,mixed> */
    public function getRequestData() : array
    {
        return $this->requestData;
    }

    /**
     * @param array<string,mixed> $requestData
     * @return $this
     */
    public function setRequestData(array $requestData) : self
    {
        $this->requestData = $requestData;
        return $this;
    }
}
