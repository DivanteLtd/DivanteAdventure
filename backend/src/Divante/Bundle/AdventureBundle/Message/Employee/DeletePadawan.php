<?php

namespace Divante\Bundle\AdventureBundle\Message\Employee;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class DeletePadawan
{
    use ObjectTrait;
    private int $leaderId;
    private int $padawanId;

    /**
     * @param int $leaderId
     * @param int $padawanId
     */
    public function __construct(
        int $leaderId,
        int $padawanId
    ) {
        $this->leaderId = $leaderId;
        $this->padawanId = $padawanId;
    }

    public function getLeaderId(): int
    {
        return $this->leaderId;
    }

    public function getPadawanId(): int
    {
        return $this->padawanId;
    }
}
