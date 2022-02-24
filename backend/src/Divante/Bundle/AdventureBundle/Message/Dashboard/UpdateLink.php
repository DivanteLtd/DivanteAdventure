<?php

namespace Divante\Bundle\AdventureBundle\Message\Dashboard;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class UpdateLink
{
    use ObjectTrait;

    protected int $id;
    protected string $title;
    protected string $url;
    protected int $authorId;

    public function __construct(int $id, string $title, string $url, int $authorId)
    {
        $this->id = $id;
        $this->title = $title;
        $this->url = $url;
        $this->authorId = $authorId;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getAuthorId(): int
    {
        return $this->authorId;
    }
}
