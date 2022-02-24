<?php

namespace Divante\Bundle\AdventureBundle\Message\Employee;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class CreateLeaderStructure
{
    use ObjectTrait;
    private int $leaderId;
    /** * @var int[] */
    private array $leaderStructure;

    /**
     * @param int $leaderId
     * @param int[] $leaderStructure
     */
    public function __construct(
        int $leaderId,
        array $leaderStructure
    ) {
        $this->leaderId = $leaderId;
        $this->leaderStructure = $leaderStructure;
    }

    public function getLeaderId(): int
    {
        return $this->leaderId;
    }

    public function getLeaderStructure(): array
    {
        return $this->leaderStructure;
    }
}
