<?php


namespace Divante\Bundle\AdventureBundle\Message\Dashboard;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class EditPost
{
    use ObjectTrait;

    protected int $id;
    protected ?string $title;
    protected ?string $banner;
    protected string $description;
    protected int $type;

    public function __construct(int $id, ?string $title, ?string $banner, string $description, int $type)
    {
        $this->id = $id;
        $this->title = $title;
        $this->banner = $banner;
        $this->description = $description;
        $this->type = $type;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getBanner() : ?string
    {
        return $this->banner;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getType() : int
    {
        return $this->type;
    }
}
