<?php

namespace Divante\Bundle\AdventureBundle\Message\Hardware;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class SignHardwareAgreement
{
    use ObjectTrait;

    private int $agreementId;
    private string $password;
    private Employee $signer;

    public function __construct(int $agreementId, string $password, Employee $signer)
    {
        $this->agreementId = $agreementId;
        $this->password = $password;
        $this->signer = $signer;
    }

    public function getAgreementId(): int
    {
        return $this->agreementId;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getSigner(): Employee
    {
        return $this->signer;
    }
}
