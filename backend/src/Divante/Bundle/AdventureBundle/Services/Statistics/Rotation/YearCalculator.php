<?php

namespace Divante\Bundle\AdventureBundle\Services\Statistics\Rotation;

class YearCalculator extends AbstractCalculator
{

    /**
     * @param int $year
     * @return array<int,array<string,mixed>>
     * @throws \Exception
     */
    public function calculate(int $year): array
    {
        /** @var EmployeeEntry[] $employeeEntries */
        $employeeEntries = $this->getEmployeeEntries();
        $months = [];
        for ($month = 1; $month <= 12; $month++) {
            $workingEmployeeEntries = array_filter(
                $employeeEntries,
                function (EmployeeEntry $entry) use ($year, $month) {
                    return $entry->isWorkingOn($year, $month);
                }
            );
            $resultForMonth = $this->calculateForTime($workingEmployeeEntries, $year, $month);
            $resultForMonth['year'] = $year;
            $resultForMonth['month'] = $month;
            $months[] = $resultForMonth;
        }
        return $months;
    }
}
