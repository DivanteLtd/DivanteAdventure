<?php

namespace Divante\Bundle\AdventureBundle\Services\Statistics\Rotation;

class MonthCalculator extends AbstractCalculator
{
    /**
     * @param int $year
     * @param int $month
     * @return array<int,array<string,mixed>>
     * @throws \Exception
     */
    public function calculate(int $year, int $month) : array
    {
        /** @var EmployeeEntry[] $employeeEntries */
        $employeeEntries = array_filter(
            $this->getEmployeeEntries(),
            function (EmployeeEntry $entry) use ($year, $month) {
                return $entry->isWorkingOn($year, $month);
            }
        );
        /** @var string[] $tribes */
        $tribes = array_map(
            function (EmployeeEntry $entry) {
                return $entry->getTribeName();
            },
            $employeeEntries
        );
        $tribes = array_unique($tribes);
        $result = [];

        foreach ($tribes as $tribe) {
            $employeesInTribe = array_filter(
                $employeeEntries,
                function (EmployeeEntry $entry) use ($year, $month, $tribe) {
                    return $entry->isWorkingOn($year, $month) && $entry->getTribeName() === $tribe;
                }
            );
            $resultForTribe = $this->calculateForTime($employeesInTribe, $year, $month);
            $resultForTribe['year'] = $year;
            $resultForTribe['month'] = $month;
            $resultForTribe['tribe'] = $tribe;
            $result[] = $resultForTribe;
        }
        return $result;
    }
}
