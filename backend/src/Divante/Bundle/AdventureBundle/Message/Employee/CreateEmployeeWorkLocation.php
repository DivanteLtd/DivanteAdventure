<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\Message\Employee;

use DateTime;
use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class CreateEmployeeWorkLocation
{
    use ObjectTrait;

    private int $userId;
    private int $type;
    private array $dates;
    private array $managers;

    public function __construct(int $userId, int $type, array $dates, array $managers)
    {
        $this->userId = $userId;
        $this->type = $type;
        $this->dates = $dates;
        $this->managers = $managers;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getDates(): array
    {
        return $this->dates;
    }

    public function getManagers(): array
    {
        return $this->managers;
    }
}
