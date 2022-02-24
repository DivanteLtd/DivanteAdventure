<?php

namespace Divante\Bundle\AdventureBundle\Message;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class UpdateConfig
{
    use ObjectTrait;

    /** @var array<string,string> */
    private array $entries;
    private int $employeeId;

    /**
     * UpdateConfig constructor.
     * @param array<string,string> $entries
     * @param int $employeeId
     */
    public function __construct(array $entries, int $employeeId)
    {
        $this->entries = $entries;
        $this->employeeId = $employeeId;
    }

    public function getEmployeeId(): int
    {
        return $this->employeeId;
    }

    public function getEntries(): array
    {
        return $this->entries;
    }
}
