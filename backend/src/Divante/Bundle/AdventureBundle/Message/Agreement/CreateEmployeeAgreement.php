<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 * Date: 26.03.19
 * Time: 08:29
 */

namespace Divante\Bundle\AdventureBundle\Message\Agreement;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class CreateEmployeeAgreement
{
    use ObjectTrait;

    private int $employeeId;
    private int $agreementId;

    public function __construct(int $employeeId, int $agreementId)
    {
        $this->employeeId = $employeeId;
        $this->agreementId = $agreementId;
    }

    public function getEmployeeId(): int
    {
        return $this->employeeId;
    }

    public function getAgreementId(): int
    {
        return $this->agreementId;
    }
}
