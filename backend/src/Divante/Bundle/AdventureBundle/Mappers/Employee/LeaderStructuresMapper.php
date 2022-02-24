<?php

namespace Divante\Bundle\AdventureBundle\Mappers\Employee;

use Divante\Bundle\AdventureBundle\Entity\Employee;

class LeaderStructuresMapper
{
    private MinimalisticEmployeeMapper $minMapper;

    public function __construct(
        MinimalisticEmployeeMapper $minMapper
    ) {
        $this->minMapper = $minMapper;
    }

    /**
     * @var Employee[] $employees
     * @return array
     */
    public function mapLeaderStructuresToJson(array $employees): array
    {
        $leaders = [];
        foreach ($employees as $employee) {
            if (!$employee->getLeaderStructures()->isEmpty()) {
                $padawans = [];
                foreach ($employee->getLeaderStructures()->getValues() as $padawan) {
                    $padawans[] = $this->minMapper->mapEmployeeToJson($padawan);
                }
                array_push(
                    $leaders,
                    [
                        'leader' => $this->minMapper->mapEmployeeToJson($employee),
                        'structures' => $padawans
                    ]
                );
            }
        }
        return $leaders;
    }
}
