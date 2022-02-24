<?php


namespace Divante\Bundle\AdventureBundle\Message\Dashboard;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class CreatePost
{
    use ObjectTrait;

    protected ?string $title;
    protected ?string $banner;
    protected string $description;
    protected int $employeeId;
    protected int $type;

    public function __construct(?string $title, ?string $banner, string $description, int $type, int $employeeId)
    {
        $this->title = $title;
        $this->banner = $banner;
        $this->description = $description;
        $this->type = $type;
        $this->employeeId = $employeeId;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getBanner(): ?string
    {
        return $this->banner;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getEmployeeId(): int
    {
        return $this->employeeId;
    }

    public function getType() : int
    {
        return $this->type;
    }
}
