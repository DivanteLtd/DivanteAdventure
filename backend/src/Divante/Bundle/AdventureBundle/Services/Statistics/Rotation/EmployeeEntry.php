<?php

namespace Divante\Bundle\AdventureBundle\Services\Statistics\Rotation;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\EmployeeEndCooperation;
use Divante\Bundle\AdventureBundle\Entity\Tribe;

class EmployeeEntry
{
    public const TERMINATION_STATUS_WORKING = 0;
    public const TERMINATION_STATUS_NOT_WORKING = 1;
    public const TERMINATION_STATUS_TERMINATED_BY_EMPLOYEE = 2;
    public const TERMINATION_STATUS_TERMINATED_BY_COMPANY = 3;

    private int $gender;
    private \DateTime $hiredAt;
    private ?\DateTime $hiredTo;
    private Tribe $tribe;
    private ?string $termination;
    private ?\DateTime $birthday;
    private int $workMode;
    private ?string $contract;

    public function __construct(
        int $gender,
        \DateTime $hired,
        ?\DateTime $left,
        Tribe $tribe,
        ?string $termination,
        ?\DateTime $birthday,
        int $workMode,
        ?string $contract
    ) {
        $this->gender = $gender;
        $this->hiredAt = $hired;
        $this->hiredTo = $left;
        $this->tribe = $tribe;
        $this->termination = $termination;
        $this->birthday = $birthday;
        $this->workMode = $workMode;
        $this->contract = $contract;
    }

    public function getHiredAt() : \DateTime
    {
        return $this->hiredAt;
    }

    public function getTribeName() : string
    {
        return $this->tribe->getName();
    }

    public function isMan() : bool
    {
        return $this->gender === Employee::GENDER_MALE;
    }

    public function isWoman() : bool
    {
        return $this->gender === Employee::GENDER_FEMALE;
    }

    public function isWorkingOn(int $year, ?int $month = null, ?int $day = null) : bool
    {
        $hiredAtYear = (int)$this->hiredAt->format('Y');
        $hiredAtMonth = (int)$this->hiredAt->format('m');
        $hiredAtDay = (int)$this->hiredAt->format('d');
        $hiredToYear = is_null($this->hiredTo) ? null : (int)$this->hiredTo->format('Y');
        $hiredToMonth = is_null($this->hiredTo) ? null : (int)$this->hiredTo->format('m');
        $hiredToDay = is_null($this->hiredTo) ? null : (int)$this->hiredTo->format('d');

        if (!is_null($day) && !is_null($month)) {
            $correctBefore = $year > $hiredAtYear
                || ($year === $hiredAtYear && $month > $hiredAtMonth)
                || ($year === $hiredAtYear && $month === $hiredAtMonth && $day > $hiredAtDay);
            $correctAfter = is_null($hiredToYear)
                || $year < $hiredToYear
                || ($year === $hiredToYear && $month < $hiredToMonth)
                || ($year === $hiredToYear && $month === $hiredToMonth && $day < $hiredToDay);
        } elseif (!is_null($month)) {
            $correctBefore = $year > $hiredAtYear
                || ($year === $hiredAtYear && $month >= $hiredAtMonth);
            $correctAfter = is_null($hiredToYear)
                || $year < $hiredToYear
                || ($year === $hiredToYear && $month <= $hiredToMonth);
        } else {
            $correctBefore = $year >= $hiredAtYear;
            $correctAfter = is_null($hiredToYear)
                || $year <= $hiredToYear;
        }

        return $correctBefore && $correctAfter;
    }

    public function hasJoinedOn(int $year, ?int $month = null) : bool
    {
        $hiredAtYear = (int)$this->hiredAt->format('Y');
        $hiredAtMonth = (int)$this->hiredAt->format('m');
        return $year === $hiredAtYear && (is_null($month) || $hiredAtMonth === $month);
    }

    public function getAge(int $year, int $month = null) : ?int
    {
        $month = $month ?? 12;
        if (is_null($this->birthday)) {
            return null;
        }

        $birthYear = (int)$this->birthday->format('Y');
        $birthMonth = (int)$this->birthday->format('m');
        $yearDiff = $year - $birthYear;
        $monthDiff = $month - $birthMonth;
        return $yearDiff * 12 + $monthDiff;
    }

    public function getContract() : ?string
    {
        return $this->contract;
    }

    public function getTerminationStatus(int $year, ?int $month = null) : int
    {
        if (is_null($this->hiredTo)) {
            return self::TERMINATION_STATUS_WORKING;
        }
        $hiredToYear = (int)$this->hiredTo->format('Y');
        $hiredToMonth = (int)$this->hiredTo->format('m');
        $month = $month ?? $hiredToMonth;
        if ($year !== $hiredToYear || $month !== $hiredToMonth) {
            return self::TERMINATION_STATUS_WORKING;
        } elseif (is_null($this->termination)) {
            return self::TERMINATION_STATUS_NOT_WORKING;
        } elseif ($this->termination === EmployeeEndCooperation::ENDED_BY_EMPLOYEE) {
            return self::TERMINATION_STATUS_TERMINATED_BY_EMPLOYEE;
        } elseif ($this->termination === EmployeeEndCooperation::ENDED_BY_COMPANY) {
            return self::TERMINATION_STATUS_TERMINATED_BY_COMPANY;
        } else {
            throw new \Exception("Unrecognized situation: year = $year, entry = ".var_export($this, true));
        }
    }

    public function getWorkTimeInMonths(int $year, ?int $month = null) : int
    {
        $month = $month ?? 12;
        $startingYear = (int)$this->getHiredAt()->format('Y');
        $startingMonth = (int)$this->getHiredAt()->format('m');
        $finishingYear = $year;
        $finishingMonth = $month;
        if ($this->getTerminationStatus($year) !== self::TERMINATION_STATUS_WORKING) {
            $terminationYear = (int)$this->hiredTo->format('Y');
            $terminationMonth = (int)$this->hiredTo->format('m');
            $finishingYear = $finishingYear > $terminationYear ? $finishingYear : $terminationYear;
            $finishingMonth = $finishingMonth > $terminationMonth ? $finishingMonth : $terminationMonth;
        }

        $yearDiff = $finishingYear - $startingYear;
        $monthDiff = $finishingMonth - $startingMonth;
        return $yearDiff * 12 + $monthDiff;
    }

    public function getWorkMode() : int
    {
        return $this->workMode;
    }

    public static function createFromEmployee(Employee $employee) : ?EmployeeEntry
    {
        if (is_null($employee->getGender()) || is_null($employee->getHiredAt()) || is_null($employee->getTribe())) {
            return null;
        }
        $gender = $employee->getGender();
        $hiredAt = $employee->getHiredAt();
        $tribe = $employee->getTribe();
        $hiredTo = null;
        $terminationSource = null;
        $birthday = is_null($employee->getDateOfBirth()) ? null : $employee->getDateOfBirth();
        $contractName = Employee::getContractNameById($employee->getContractId());

        $terminationEntity = $employee->getEndingCooperationEntity();
        if (!is_null($employee->getHiredTo()) && !is_null($terminationEntity)) {
            $hiredTo = $employee->getHiredTo();
            $terminationSource = $terminationEntity->getWhoEndedCooperation();
        } elseif (!is_null($terminationEntity)) {
            $hiredTo = $terminationEntity->getDismissDate();
            $terminationSource = $terminationEntity->getWhoEndedCooperation();
        } elseif (!is_null($employee->getHiredTo())) {
            $hiredTo = $employee->getHiredTo();
        }

        return new EmployeeEntry(
            $gender,
            $hiredAt,
            $hiredTo,
            $tribe,
            $terminationSource,
            $birthday,
            $employee->getWorkMode(),
            $contractName
        );
    }
}
