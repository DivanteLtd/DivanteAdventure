<?php
namespace Divante\Bundle\AdventureBundle\Query\Statistic;

class EmployeeView
{
    /** @var string */
    protected $tribeName;

    /** @var \DateTime */
    protected $date;

    /** @var integer */
    protected $numberOfEnter;

    /** @var integer */
    protected $numberOfLeave;

    /** @var integer */
    protected $numberOfWork;

    /**
     * EmployeeView constructor.
     * @param string $tribeName
     * @param \DateTime $date
     * @param int $numberOfEnter
     * @param int $numberOfLeave
     * @param int $numberOfWork
     */
    public function __construct(
        string $tribeName,
        \DateTime $date,
        int $numberOfEnter,
        int $numberOfLeave,
        int $numberOfWork
    ) {
        $this->tribeName = $tribeName;
        $this->date = $date;
        $this->numberOfEnter = $numberOfEnter;
        $this->numberOfLeave = $numberOfLeave;
        $this->numberOfWork = $numberOfWork;
    }

    /**
     * @return string
     */
    public function getTribeName(): string
    {
        return $this->tribeName;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @return int
     */
    public function getNumberOfEnter(): int
    {
        return $this->numberOfEnter;
    }

    /**
     * @return int
     */
    public function getNumberOfLeave(): int
    {
        return $this->numberOfLeave;
    }

    /**
     * @return int
     */
    public function getNumberOfWork(): int
    {
        return $this->numberOfWork;
    }
}
