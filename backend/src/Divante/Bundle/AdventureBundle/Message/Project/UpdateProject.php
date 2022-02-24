<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 * Date: 13.02.19
 * Time: 11:55
 */

namespace Divante\Bundle\AdventureBundle\Message\Project;

use Divante\Bundle\AdventureBundle\Entity\Project;
use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class UpdateProject
{
    use ObjectTrait;

    private Project $entry;
    private string $name;
    private string $description;
    private string $photo;
    private string $url;
    private int $type;
    private int $budget;
    private ?string $startedAt;
    private ?string $endedAt;
    private bool $billable;
    private string $code;
    /** @var int[] */
    private array $gitlabProjects;

    /**
     * UpdateProject constructor.
     * @param Project $entry
     * @param string $name
     * @param string $code
     * @param string $description
     * @param string $photo
     * @param string $url
     * @param int $type
     * @param int $budget
     * @param bool $billable
     * @param string|null $startedAt
     * @param string|null $endedAt
     * @param int[] $gitlabProjects
     */
    public function __construct(
        Project $entry,
        string $name,
        string $code,
        string $description,
        string $photo,
        string $url,
        int $type,
        int $budget,
        bool $billable,
        ?string $startedAt,
        ?string $endedAt,
        array $gitlabProjects
    ) {
        $this->entry = $entry;
        $this->name = $name;
        $this->description = $description;
        $this->photo = $photo;
        $this->url = $url;
        $this->type = $type;
        $this->budget = $budget;
        $this->startedAt = $startedAt;
        $this->endedAt = $endedAt;
        $this->billable = $billable;
        $this->code = $code;
        $this->gitlabProjects = $gitlabProjects;
    }

    public function getEntry(): Project
    {
        return $this->entry;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPhoto(): string
    {
        return $this->photo;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getBudget(): int
    {
        return $this->budget;
    }

    public function getStartedAt(): ?string
    {
        return $this->startedAt;
    }

    public function getEndedAt(): ?string
    {
        return $this->endedAt;
    }

    public function isBillable(): bool
    {
        return $this->billable;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    /** @return int[] */
    public function getGitlabProjects() : array
    {
        return $this->gitlabProjects;
    }
}
