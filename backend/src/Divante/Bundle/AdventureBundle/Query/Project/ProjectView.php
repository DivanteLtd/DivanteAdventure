<?php
/**
 * Created by PhpStorm.
 * User: pawel
 * Date: 16.01.19
 * Time: 12:20
 */

namespace Divante\Bundle\AdventureBundle\Query\Project;

class ProjectView
{
    /** @var integer */
    private $id;
    /** @var string */
    private $name;
    /** @var string|null */
    private $startedAt;
    /** @var string|null */
    private $endedAt;
    /** @var boolean */
    private $delete;

    /**
     * ProjectView constructor.
     *
     * @param int $id
     * @param string $name
     * @param string|null $startedAt
     * @param string|null $endedAt
     * @param bool $delete
     */
    public function __construct(int $id, string $name, ?string $startedAt, ?string $endedAt, bool $delete)
    {
        $this->id = $id;
        $this->name = $name;
        $this->startedAt = $startedAt;
        $this->endedAt = $endedAt;
        $this->delete = $delete;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getStartedAt(): ?string
    {
        return $this->startedAt;
    }

    /**
     * @return string
     */
    public function getEndedAt(): ?string
    {
        return $this->endedAt;
    }

    /**
     * @return bool
     */
    public function isDelete(): bool
    {
        return $this->delete;
    }
}
