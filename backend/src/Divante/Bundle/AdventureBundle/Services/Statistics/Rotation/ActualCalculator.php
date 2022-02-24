<?php

namespace Divante\Bundle\AdventureBundle\Services\Statistics\Rotation;

class ActualCalculator extends AbstractCalculator
{
    /**
     * @return array<string,mixed>
     * @throws \Exception
     */
    public function calculate() : array
    {
        $year = (int)date('Y');
        $month = (int)date('m');
        $day = (int)date('d');
        /** @var EmployeeEntry[] $employeeEntries */
        $employeeEntries = $this->getEmployeeEntries();
        $workingEmployeeEntries = array_filter(
            $employeeEntries,
            function (EmployeeEntry $entry) use ($year, $month, $day) {
                return $entry->isWorkingOn($year, $month, $day);
            }
        );
        $result = $this->calculateForTime($workingEmployeeEntries, $year, $month);
        return $result;
    }
}
