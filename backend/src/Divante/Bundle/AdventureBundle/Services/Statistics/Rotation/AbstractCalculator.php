<?php

namespace Divante\Bundle\AdventureBundle\Services\Statistics\Rotation;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractCalculator
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return EmployeeEntry[]
     */
    protected function getEmployeeEntries() : array
    {
        $employeeRepo = $this->em->getRepository(Employee::class);
        /** @var Employee[] $employees */
        $employees = $employeeRepo->findAll();
        /** @var EmployeeEntry[] $employeeEntries */
        $employeeEntries = [];
        foreach ($employees as $employee) {
            $entry = EmployeeEntry::createFromEmployee($employee);
            if (!is_null($entry)) {
                $employeeEntries[] = $entry;
            }
        }
        return $employeeEntries;
    }

    /**
     * @param EmployeeEntry[] $employees
     * @param int $year
     * @param int|null $month
     * @return array<string,mixed>
     * @throws \Exception
     */
    protected function calculateForTime(array $employees, int $year, ?int $month = null) : array
    {
        $men = 0;
        $women = 0;
        $joined = 0;
        $workTime = 0;
        $age = 0;
        $ageCount = 0;
        $menAge = 0;
        $menAgeCount = 0;
        $womenAge = 0;
        $womenAgeCount = 0;
        $workRemotely = 0;
        $workFromOffice = 0;
        $workPartialRemotely = 0;

        $leaveByUnknownReasons = 0;
        $leaveByComany = 0;
        $leaveByEmployee = 0;
        $contracts = [];

        /** @var EmployeeEntry $employee */
        foreach ($employees as $employee) {
            if ($employee->hasJoinedOn($year, $month)) {
                $joined++;
            }
            $workTime += $employee->getWorkTimeInMonths($year, $month);

            $terminationStatus = $employee->getTerminationStatus($year, $month);
            if ($terminationStatus === EmployeeEntry::TERMINATION_STATUS_NOT_WORKING) {
                $leaveByUnknownReasons++;
            } elseif ($terminationStatus === EmployeeEntry::TERMINATION_STATUS_TERMINATED_BY_EMPLOYEE) {
                $leaveByEmployee++;
            } elseif ($terminationStatus === EmployeeEntry::TERMINATION_STATUS_TERMINATED_BY_COMPANY) {
                $leaveByComany++;
            } else {
                $employeeAge = $employee->getAge($year, $month) ?? 0;
                $ageIncrementation = $employeeAge === 0 ? 0 : 1;
                $age += $employeeAge;
                $ageCount += $ageIncrementation;
                if ($employee->isMan()) {
                    $men++;
                    $menAge += $employeeAge;
                    $menAgeCount += $ageIncrementation;
                } elseif ($employee->isWoman()) {
                    $women++;
                    $womenAge += $employeeAge;
                    $womenAgeCount += $ageIncrementation;
                }
                if ($employee->getWorkMode()) {
                    switch ($employee->getWorkMode()) {
                        case Employee::WORK_FROM_OFFICE:
                            $workFromOffice++;
                            break;
                        case Employee::WORK_REMOTELY:
                            $workRemotely++;
                            break;
                        case Employee::WORK_PARTIAL_REMOTELY:
                            $workPartialRemotely++;
                            break;
                        default:
                            return [];
                    }
                }
                $contract = $employee->getContract();
                if (!is_null($contract)) {
                    $currentVal = $contracts[$contract] ?? 0;
                    $contracts[$contract] = $currentVal + 1;
                }
            }
        }

        $peopleSum = $men + $women;
        $leftSum = $leaveByComany + $leaveByEmployee + $leaveByUnknownReasons;

        return [
            'men' => $men,
            'women' => $women,
            'joined' => $joined,
            'left' => $leftSum,
            'balance' => $joined - $leftSum,
            'mediumAge' => $ageCount === 0 ? 0 : $age / ($ageCount * 12),
            'womenMediumAge' => $womenAgeCount === 0 ? 0 : $womenAge / ($womenAgeCount * 12),
            'menMediumAge' => $menAgeCount === 0 ? 0 : $menAge / ($menAgeCount * 12),
            'pri' => $peopleSum === 0 ? 0 : $joined * 100 / $peopleSum,
            'workTime' => $peopleSum === 0 ? 0 : $workTime / ($peopleSum * 12),
            'terminatedByCompany' => $leaveByComany,
            'terminatedByEmployee' => $leaveByEmployee,
            'workRemotely' => $workRemotely,
            'workFromOffice' => $workFromOffice,
            'workPartialRemotely' => $workPartialRemotely,
            'contracts' => $contracts,
            'definedWorkModeSum' => $workRemotely + $workFromOffice + $workPartialRemotely
        ];
    }
}
