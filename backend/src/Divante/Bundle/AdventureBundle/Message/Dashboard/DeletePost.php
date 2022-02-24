<?php


namespace Divante\Bundle\AdventureBundle\Message\Dashboard;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class DeletePost
{
    use ObjectTrait;

    private int $id;
    private int $deletingEmployeeId;

    public function __construct(int $id, int $deletingEmployeeId)
    {
        $this->id = $id;
        $this->deletingEmployeeId = $deletingEmployeeId;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDeletingEmployeeId(): int
    {
        return $this->deletingEmployeeId;
    }
}
