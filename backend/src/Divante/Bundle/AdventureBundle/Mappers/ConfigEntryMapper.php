<?php

namespace Divante\Bundle\AdventureBundle\Mappers;

use Divante\Bundle\AdventureBundle\Entity\ConfigEntry;
use Divante\Bundle\AdventureBundle\Mappers\Employee\MinimalisticEmployeeMapper;

class ConfigEntryMapper
{
    private MinimalisticEmployeeMapper $employeeMapper;

    public function __construct(MinimalisticEmployeeMapper $employeeMapper)
    {
        $this->employeeMapper = $employeeMapper;
    }

    public function __invoke(ConfigEntry $entry): array
    {
        $responsible = $entry->getResponsible();
        $createdAt = $entry->getCreatedAt();
        $replacedAt = $entry->getReplacedAt();
        [ $group, $name ] = explode('.', $entry->getKey(), 2);
        return [
            'value' => $entry->getValue(),
            'key' => $entry->getKey(),
            'group' => $group,
            'name' => $name,
            'createdAt' => $createdAt->getTimestamp(),
            'replacedAt' => is_null($replacedAt) ? null : $replacedAt->getTimestamp(),
            'responsible' => is_null($responsible) ? null : ($this->employeeMapper)($responsible),
        ];
    }
}
