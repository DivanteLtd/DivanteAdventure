<?php
namespace Divante\Bundle\AdventureBundle\Query\Statistic;

class EmployeeGeneralView
{
    /** @var string */
    private $name;
    /** @var string */
    private $lastName;
    /** @var string|null */
    private $hiredAt;
    /** @var string|null */
    private $hiredTo;
    /** @var integer */
    private $gender;
    /** @var integer */
    private $tribeId;
    /** @var string */
    private $tribeName;
    /** @var string|null */
    private $whoEndedCooperation;
    /** @var string|null */
    private $dismissDate;

    /**
     * EmployeeView constructor.
     * @param string $name
     * @param string $lastName
     * @param string|null $hiredAt
     * @param string|null $hiredTo
     * @param int|null $gender
     * @param int|null $tribeId
     * @param string $tribeName
     */
    public function __construct(
        string $name,
        string $lastName,
        ?string $hiredAt,
        ?string $hiredTo,
        ?int $gender,
        ?int $tribeId,
        string $tribeName,
        ?string $whoEndedCooperation,
        ?string $dismissDate
    ) {
        $this->name = $name;
        $this->lastName = $lastName;
        $this->hiredAt = $hiredAt;
        $this->hiredTo = $hiredTo;
        $this->gender = $gender;
        $this->tribeId = $tribeId;
        $this->tribeName = $tribeName;
        $this->whoEndedCooperation = $whoEndedCooperation;
        $this->dismissDate = $dismissDate;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return string|null
     */
    public function getHiredAt(): ?string
    {
        return $this->hiredAt;
    }

    /**
     * @return string|null
     */
    public function getHiredTo(): ?string
    {
        return $this->hiredTo;
    }

    /**
     * @return int|null
     */
    public function getGender(): ?int
    {
        return $this->gender;
    }

    /**
     * @return int|null
     */
    public function getTribeId(): ?int
    {
        return $this->tribeId;
    }

    /**
     * @return string
     */
    public function getTribeName(): string
    {
        return $this->tribeName;
    }

    /**
     * @return string|null
     */
    public function getWhoEndedCooperation(): ?string
    {
        return $this->whoEndedCooperation;
    }

    /**
     * @return string|null
     */
    public function getDismissDate(): ?string
    {
        return $this->dismissDate;
    }
}
