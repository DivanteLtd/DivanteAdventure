<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 * Date: 04.01.19
 * Time: 09:39
 */

namespace Divante\Bundle\AdventureBundle\Message\Agreement;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class DeleteEmployeeAgreement
{
    use ObjectTrait;

    private int $employeeId;

    public function __construct(int $employeeId)
    {
        $this->employeeId = $employeeId;
    }

    public function getEmployeeId(): int
    {
        return $this->employeeId;
    }
}
