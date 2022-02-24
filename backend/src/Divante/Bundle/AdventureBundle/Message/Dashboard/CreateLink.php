<?php

namespace Divante\Bundle\AdventureBundle\Message\Dashboard;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class CreateLink
{
    use ObjectTrait;

    private string $title;
    private string $url;
    private int $userId;

    public function __construct(string $title, string $url, int $userId)
    {
        $this->title = $title;
        $this->url = $url;
        $this->userId = $userId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}
