<?php
/**
 * Created by PhpStorm.
 * User: pawel
 * Date: 11.01.19
 * Time: 14:09
 */
namespace Divante\Bundle\AdventureBundle\Query\Employee;

class EmployeeView
{
    private int $id;
    private string $name;
    private string $lastName;
    private int $worktime;
    private ?string $hiredTo;
    private ?string $tribeName;
    private ?string $positionName;
    private ?string $levelName;
    private ?string $projectName;
    /** @var string[] */
    private array $filters;

    /**
     * @param int $id
     * @param string $name
     * @param string $lastName
     * @param int $workTime
     * @param string|null $hiredTo
     * @param string|null $tribeName
     * @param string|null $positionName
     * @param string|null $levelName
     * @param string|null $projectName
     * @param string[] $filters
     */
    public function __construct(
        int $id,
        string $name,
        string $lastName,
        int $workTime,
        ?string $hiredTo,
        ?string $tribeName,
        ?string $positionName,
        ?string $levelName,
        ?string $projectName,
        array $filters
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->lastName = $lastName;
        $this->worktime = $workTime;
        $this->hiredTo = $hiredTo;
        $this->filters = $filters;
        $this->tribeName = $tribeName;
        $this->positionName = $positionName;
        $this->projectName = $projectName;
        $this->levelName = $levelName;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getWorktime(): int
    {
        return $this->worktime;
    }

    public function getHiredTo(): ?string
    {
        return $this->hiredTo;
    }

    /** @return string[] */
    public function getFilters(): array
    {
        return $this->filters;
    }

    public function getTribeName(): ?string
    {
        return $this->tribeName;
    }

    public function getPositionName(): ?string
    {
        return $this->positionName;
    }

    public function getLevelName(): ?string
    {
        return $this->levelName;
    }

    public function getProjectName(): ?string
    {
        return $this->projectName;
    }
}
