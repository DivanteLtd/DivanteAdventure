<?php

namespace Divante\Bundle\AdventureBundle\Services\Statistics\Rotation;

class WholeTimeCalculator extends AbstractCalculator
{
    /**
     * @return array<int,array<string,mixed>>
     * @throws \Exception
     */
    public function calculate() : array
    {
        /** @var EmployeeEntry[] $employeeEntries */
        $employeeEntries = $this->getEmployeeEntries();
        $earliestYear = (int)date('Y');
        foreach ($employeeEntries as $entry) {
            $hiredAtYear = (int)$entry->getHiredAt()->format('Y');
            if ($hiredAtYear < $earliestYear) {
                $earliestYear = $hiredAtYear;
            }
        }
        $years = [];
        for ($year = $earliestYear; $year <= (int)date('Y'); $year++) {
            $workingEmployeeEntries = array_filter(
                $employeeEntries,
                function (EmployeeEntry $entry) use ($year) {
                    return $entry->isWorkingOn($year);
                }
            );
            $resultForYear = $this->calculateForTime($workingEmployeeEntries, $year);
            $resultForYear['year'] = $year;
            $years[] = $resultForYear;
        }
        usort($years, function ($a, $b) {
            return $b['year'] - $a['year'];
        });
        return $years;
    }
}
