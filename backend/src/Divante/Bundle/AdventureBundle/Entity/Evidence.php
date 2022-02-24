<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 31.01.19
 * Time: 13:47
 */

namespace Divante\Bundle\AdventureBundle\Entity;

use DateTime;
use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

/**
 * Class Evidence
 * @package Divante\Bundle\AdventureBundle\Entity
 * @ORM\Entity()
 * @ORM\Table(name="evidence")
 */
class Evidence
{
    use Id;

    public const STATUS_EVIDENCE_NOT_SENT = 0;
    public const STATUS_EVIDENCE_SENT = 1;

    public const STATUS_OVERTIME_APPROVAL_NOT_REQUIRED = 0;
    public const STATUS_OVERTIME_AWAITS_APPROVAL = 1;
    public const STATUS_OVERTIME_NOT_APPROVED = 2;
    public const STATUS_OVERTIME_SENT = 3;

    /** @ORM\Column(name="month", type="smallint") */
    private int $month;
    /** @ORM\Column(name="year", type="smallint") */
    private int $year;
    /** @ORM\Column(name="working_hours", type="decimal", precision=5, scale=2) */
    private string $workingHours;
    /** @ORM\Column(name="payed_free_hours", type="decimal", precision=5, scale=2) */
    private string $paidFreeHours;
    /** @ORM\Column(name="unpayed_free_hours", type="decimal", precision=5, scale=2) */
    private string $unpaidFreeHours;
    /** @ORM\Column(name="sick_leave_hours", type="decimal", precision=5, scale=2) */
    private string $sickLeaveHours;
    /** @ORM\Column(name="unavailability_hours", type="decimal", precision=5, scale=2) */
    private string $unavailabilityHours;
    /** @ORM\Column(name="evidence_status", type="smallint") */
    private int $evidenceStatus = self::STATUS_EVIDENCE_NOT_SENT;
    /** @ORM\Column(name="overtime_status", type="smallint") */
    private int $overtimeStatus = self::STATUS_OVERTIME_APPROVAL_NOT_REQUIRED;
    /** @ORM\Column(name="created_at", type="datetime", nullable=true) */
    private ?DateTime $createdAt = null;
    /** @ORM\Column(name="updated_at", type="datetime", nullable=true) */
    private ?DateTime $updatedAt = null;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="Divante\Bundle\AdventureBundle\Entity\Employee",
     *     inversedBy="employeeEvidences",
     *     cascade={"persist"}
     * )
     * @ORM\JoinColumn(name="employee_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private Employee $employee;

    /**
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Employee", inversedBy="evidenceRequests")
     * @ORM\JoinColumn(name="manager_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private ?Employee $overtimeManager = null;

    /**
     * @var Collection<int,EvidenceOvertime>
     * @ORM\OneToMany(
     *     targetEntity="Divante\Bundle\AdventureBundle\Entity\EvidenceOvertime",
     *     mappedBy="evidence",
     *     cascade={"remove"}
     * )
     */
    private Collection $overtimeEntries;

    /**
     * @var Collection<int,EvidenceInvoice>
     * @ORM\OneToMany(targetEntity="Divante\Bundle\AdventureBundle\Entity\EvidenceInvoice", mappedBy="evidence")
     */
    private Collection $invoices;

    public function __construct()
    {
        $this->invoices = new ArrayCollection();
        $this->overtimeEntries = new ArrayCollection();
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

    public function getMonth() : int
    {
        return $this->month;
    }

    public function setMonth(int $month) : self
    {
        $this->month = $month;
        return $this;
    }

    public function getYear() : int
    {
        return $this->year;
    }

    public function setYear(int $year) : self
    {
        $this->year = $year;
        return $this;
    }

    public function getWorkingHours() : string
    {
        return $this->workingHours;
    }

    public function setWorkingHours(string $workingHours) : self
    {
        $this->workingHours = $workingHours;
        return $this;
    }

    public function getPaidFreeHours() : string
    {
        return $this->paidFreeHours;
    }

    public function setPaidFreeHours(string $paidFreeHours) : self
    {
        $this->paidFreeHours = $paidFreeHours;
        return $this;
    }

    public function getUnpaidFreeHours() : string
    {
        return $this->unpaidFreeHours;
    }

    public function setUnpaidFreeHours(string $unpaidFreeHours) : self
    {
        $this->unpaidFreeHours = $unpaidFreeHours;
        return $this;
    }

    public function getSickLeaveHours() : string
    {
        return $this->sickLeaveHours;
    }

    public function setSickLeaveHours(string $sickLeaveHours) : self
    {
        $this->sickLeaveHours = $sickLeaveHours;
        return $this;
    }

    public function getUnavailabilityHours() : string
    {
        return $this->unavailabilityHours;
    }

    public function setUnavailabilityHours(string $unavailabilityHours) : self
    {
        $this->unavailabilityHours = $unavailabilityHours;
        return $this;
    }

    public function getOvertimeManager() : ?Employee
    {
        return $this->overtimeManager;
    }

    public function setOvertimeManager(?Employee $manager) : self
    {
        $this->overtimeManager = $manager;
        return $this;
    }

    /** @return Collection<int,EvidenceOvertime> */
    public function getOvertimeEntries(): Collection
    {
        return $this->overtimeEntries;
    }

    /** @return EvidenceInvoice[] */
    public function getInvoices() : array
    {
        return $this->invoices->toArray();
    }

    public function addInvoice(EvidenceInvoice $invoice) : self
    {
        $this->invoices->add($invoice);
        return $this;
    }

    public function getEvidenceStatus() : int
    {
        return $this->evidenceStatus;
    }

    public function setEvidenceStatus(int $status) : self
    {
        $this->evidenceStatus = $status;
        return $this;
    }

    public function getOvertimeStatus() : int
    {
        return $this->overtimeStatus;
    }

    public function setOvertimeStatus(int $status) : self
    {
        $this->overtimeStatus = $status;
        return $this;
    }

    public function getCreatedAt() : ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt() : self
    {
        if (is_null($this->createdAt)) {
            $this->createdAt = new DateTime();
        }
        return $this;
    }

    public function getUpdatedAt() : ?DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt() : self
    {
        $this->updatedAt = new DateTime();
        return $this;
    }
}
