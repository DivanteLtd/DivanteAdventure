<?php

namespace Divante\Bundle\AdventureBundle\Message\Employee;

abstract class AbstractPotentialEmployeeMessage
{

    private ?int $designatedTribeId;
    private ?int $designatedPositionId;
    private ?string $designatedHireDate;
    private ?string $dateOfBirth;
    private ?string $welcomeDay;
    private ?string $language;

    public function __construct(
        ?string $hireDate,
        ?int $tribeId,
        ?int $positionId,
        ?string $dateOfBirth,
        ?string $welcomeDay,
        ?string $language
    ) {
        $this->designatedHireDate = $hireDate;
        $this->designatedTribeId = $tribeId;
        $this->designatedPositionId = $positionId;
        $this->dateOfBirth = $dateOfBirth;
        $this->welcomeDay = $welcomeDay;
        $this->language = $language;
    }

    public function getDesignatedHireDate() : ?string
    {
        return $this->designatedHireDate;
    }

    public function getDateOfBirth() : ?string
    {
        return $this->dateOfBirth;
    }

    public function getDesignatedTribeId() : ?int
    {
        return $this->designatedTribeId;
    }

    public function getDesignatedPositionId() : ?int
    {
        return $this->designatedPositionId;
    }

    public function getWelcomeDay(): ?string
    {
        return $this->welcomeDay;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }
}
