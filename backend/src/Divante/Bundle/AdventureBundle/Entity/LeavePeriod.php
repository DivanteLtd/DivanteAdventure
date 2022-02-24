<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 28.01.19
 * Time: 07:32
 */

namespace Divante\Bundle\AdventureBundle\Entity;

use DateTime;
use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Divante\Bundle\AdventureBundle\Entity\Data\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class LeavePeriod
 * @package Divante\Bundle\AdventureBundle\Entity
 * @ORM\Table(name="leave_period")
 * @ORM\Entity(repositoryClass="Divante\Bundle\AdventureBundle\Entity\Repository\LeavePeriodRepository")
 */
class LeavePeriod
{
    public const DATE_FORMAT = 'Y-m-d';

    use Timestampable, Id;

    /** @Assert\NotBlank @ORM\Column(name="date_from", type="date", nullable=false) */
    private DateTime $dateFrom;

    /** @Assert\NotBlank @ORM\Column(name="date_to", type="date", nullable=false) */
    private DateTime $dateTo;

    /** @ORM\Column(name="comment_system", type="text", length=65535, nullable=true) */
    private ?string $commentSystem = null;

    /** @Assert\NotBlank @ORM\Column(name="sick_leave_days", type="integer", nullable=false) */
    private int $sickLeaveDays = 0;

    /** @Assert\NotBlank @ORM\Column(name="freedays", type="integer", nullable=false) */
    private int $freeDays = 0;

    /**
     * @Assert\NotBlank
     * @ORM\ManyToOne(targetEntity="Employee", inversedBy="periods")
     * @ORM\JoinColumn(name="employee_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private Employee $employee;

    /**
     * @var PersistentCollection<int,LeaveRequest>
     * @ORM\OneToMany(targetEntity="Divante\Bundle\AdventureBundle\Entity\LeaveRequest", mappedBy="period")
     */
    private $requests;

    public function getDateFrom() : DateTime
    {
        return $this->dateFrom;
    }

    public function setDateFrom(DateTime $dateFrom): self
    {
        $this->dateFrom = $dateFrom;
        return $this;
    }

    public function getDateTo() : DateTime
    {
        return $this->dateTo;
    }

    public function setDateTo(DateTime $dateTo) : self
    {
        $this->dateTo = $dateTo;
        return $this;
    }

    public function getCommentSystem() : ?string
    {
        return $this->commentSystem;
    }

    public function setCommentSystem(?string $system) : self
    {
        $this->commentSystem = $system;
        return $this;
    }

    public function getEmployee() : Employee
    {
        return $this->employee;
    }

    public function setEmployee(Employee $employee) : self
    {
        $this->employee = $employee;
        return $this;
    }

    public function getFreedays() : int
    {
        return $this->freeDays;
    }

    public function setFreedays(int $freeDays) : self
    {
        $this->freeDays = $freeDays;
        return $this;
    }

    public function getSickLeaveDays() : int
    {
        return $this->sickLeaveDays;
    }

    public function setSickLeaveDays(int $sickLeaveDays) : self
    {
        $this->sickLeaveDays = $sickLeaveDays;
        return $this;
    }

    /** @return PersistentCollection<int,LeaveRequest> */
    public function getRequests() : PersistentCollection
    {
        return $this->requests;
    }
}
