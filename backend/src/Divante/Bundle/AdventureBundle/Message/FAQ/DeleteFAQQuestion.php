<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\Message\FAQ;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class DeleteFAQQuestion
{
    use ObjectTrait;
    private int $id;
    private int $employeeId;

    public function __construct(int $id, int $employeeId)
    {
        $this->id = $id;
        $this->employeeId = $employeeId;
    }

    public function getId() : int
    {
        return $this->id;
    }

    public function getEmployeeId() : int
    {
        return $this->employeeId;
    }
}
