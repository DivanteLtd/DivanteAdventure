<?php


namespace Divante\Bundle\AdventureBundle\Query\Report;

class EmployeeView
{
    protected int $id;
    protected string $firstName;
    protected string $lastName;
    /** @var ProjectView[] */
    protected array $projects;
    /** @var DayView[] */
    protected array $days;

    public function __construct(int $id, string $firstName, string $lastName)
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    /** @return ProjectView[] */
    public function getProjects(): array
    {
        return $this->projects;
    }

    /** @return DayView[] */
    public function getDays(): array
    {
        return $this->days;
    }

    public function addProject(ProjectView $project) : void
    {
        $this->projects[] = $project;
    }

    public function addDay(DayView $day) : void
    {
        $this->days[] = $day;
    }
}
