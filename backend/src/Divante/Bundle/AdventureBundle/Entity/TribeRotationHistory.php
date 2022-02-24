<?php

namespace Divante\Bundle\AdventureBundle\Entity;

use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Divante\Bundle\AdventureBundle\Entity\Data\Timestampable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class TribeRotationHistory
 * @package Divante\Bundle\AdventureBundle\Entity
 * @ORM\Table(name="tribe_rotation_history")
 * @ORM\Entity(repositoryClass="Divante\Bundle\AdventureBundle\Entity\Repository\TribeRotationHistoryRepository")
 */
class TribeRotationHistory
{
    use Timestampable, Id;

    /** @ORM\Column(name="tribe_name", type="string", length=50, nullable=false) */
    private string $tribeName;
    /** @ORM\Column(name="number_of_enter", type="integer", nullable=false,  options={"default" : 0}) */
    private int $numberOfEnter = 0;
    /** @ORM\Column(name="number_of_leave", type="integer", nullable=false,  options={"default" : 0}) */
    private int $numberOfLeave = 0;
    /** @ORM\Column(name="number_of_work", type="integer", nullable=false,  options={"default" : 0}) */
    private int $numberOfWork = 0;
    /** @ORM\Column(name="number_of_male", type="integer", nullable=false, options={"default" : 0}) */
    private int $numberOfMale = 0;
    /** @ORM\Column(name="number_of_female", type="integer", nullable=false,  options={"default" : 0}) */
    private int $numberOfFemale = 0;
    /** @ORM\Column(name="year", type="smallint", nullable=false) */
    private int $year;
    /** @ORM\Column(name="month", type="smallint", nullable=false) */
    private int $month;
    /** @ORM\Column(name="employees", type="string", nullable=false) */
    private string $employees;

    public function getTribeName(): ?string
    {
        return $this->tribeName;
    }

    public function setTribeName(string $tribeName): void
    {
        $this->tribeName = $tribeName;
    }

    public function getNumberOfEnter(): ?int
    {
        return $this->numberOfEnter;
    }

    public function setNumberOfEnter(int $numberOfEnter): void
    {
        $this->numberOfEnter = $numberOfEnter;
    }

    public function getNumberOfLeave(): ?int
    {
        return $this->numberOfLeave;
    }

    public function setNumberOfLeave(int $numberOfLeave): void
    {
        $this->numberOfLeave = $numberOfLeave;
    }

    public function getNumberOfWork(): ?int
    {
        return $this->numberOfWork;
    }

    public function setNumberOfWork(int $numberOfWork): void
    {
        $this->numberOfWork = $numberOfWork;
    }

    public function getNumberOfMale(): ?int
    {
        return $this->numberOfMale;
    }

    public function setNumberOfMale(int $numberOfMale): void
    {
        $this->numberOfMale = $numberOfMale;
    }

    public function getNumberOfFemale(): int
    {
        return $this->numberOfFemale;
    }

    public function setNumberOfFemale(int $numberOfFemale): void
    {
        $this->numberOfFemale = $numberOfFemale;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function setYear(int $year): void
    {
        $this->year = $year;
    }

    public function getMonth(): int
    {
        return $this->month;
    }

    public function setMonth(int $month): void
    {
        $this->month = $month;
    }

    public function getEmployees(): string
    {
        return $this->employees;
    }

    public function setEmployees(string $employees): void
    {
        $this->employees = $employees;
    }
}
