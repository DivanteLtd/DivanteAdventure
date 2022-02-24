<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\Message\Feedback;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class DeleteFeedback
{
    use ObjectTrait;
    private int $id;
    private Employee $employee;

    public function __construct(int $id, Employee $employee)
    {
        $this->id = $id;
        $this->employee = $employee;
    }

    public function getId() : int
    {
        return $this->id;
    }

    public function getEmployee() : Employee
    {
        return $this->employee;
    }
}
