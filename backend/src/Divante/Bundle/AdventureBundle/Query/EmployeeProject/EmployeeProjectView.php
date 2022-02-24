<?php
/**
 * Created by PhpStorm.
 * User: pawel
 * Date: 16.01.19
 * Time: 13:12
 */
namespace  Divante\Bundle\AdventureBundle\Query\EmployeeProject;

class EmployeeProjectView
{
    private int $id;
    private int $employeeId;
    private int $projectId;
    /** @var string */
    private string $employeeName;
    /** @var string */
    private $employeeLastName;
    /** @var string */
    private string $projectName;
    private ?string $startedAt;
    private ?string $endedAt;
    /** @var string[] */
    private array $dateFrom;
    /** @var string[] */
    private array $dateTo;
    private bool $overtime = false;
    private bool $deleted = false;

    public function __construct(
        int $id,
        int $employeeId,
        int $projectId,
        string $employeeName,
        string $employeeLastName,
        string $projectName,
        ?string $startedAt,
        ?string $endedAt,
        array $dateFrom,
        array $dateTo,
        bool $overtime,
        ?bool $deleted
    ) {
        $this->id = $id;
        $this->employeeId = $employeeId;
        $this->projectId = $projectId;
        $this->employeeName = $employeeName;
        $this->employeeLastName = $employeeLastName;
        $this->startedAt = $startedAt;
        $this->endedAt = $endedAt;
        $this->projectName = $projectName;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->overtime = $overtime;
        $this->deleted = $deleted ?? false;
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

    public function getEmployeeName(): string
    {
        return $this->employeeName;
    }

    public function getEmployeeLastName(): string
    {
        return $this->employeeLastName;
    }

    public function getProjectName(): string
    {
        return $this->projectName;
    }

    public function getStartedAt(): ?string
    {
        return $this->startedAt;
    }

    public function getEndedAt(): ?string
    {
        return $this->endedAt;
    }

    /** @return string[] */
    public function getDateFrom(): array
    {
        return $this->dateFrom;
    }

    /** @return string[] */
    public function getDateTo(): array
    {
        return $this->dateTo;
    }

    public function isOvertime(): bool
    {
        return $this->overtime;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }
}
