<?php
/**
 * Created by PhpStorm.
 * User: norbert
 * Date: 21.12.18
 * Time: 11:32
 */

namespace Divante\Bundle\AdventureBundle\Entity;

use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class LeaveRequestDay
 * @package Divante\Bundle\AdventureBundle\Entity
 * @ORM\Table(
 *      name="leave_request_day",
 *      indexes={@ORM\Index(name="FK_leave_request_day_leave_request", columns={"request_id"})}
 * )
 * @ORM\Entity(repositoryClass="Divante\Bundle\AdventureBundle\Entity\Repository\LeaveRequestDayRepository")
 */
class LeaveRequestDay
{
    use Id;

    public const DAY_STATUS_ACTIVE = 0;
    public const DAY_STATUS_CANCELED = 1;
    public const DAY_STATUS_PENDING_RESIGNATION = 2;
    public const DAY_STATUS_RESIGNED = 3;

    public const DAY_TYPE_FREE_PAID = 0; // paid day off
    public const DAY_TYPE_FREE_UNPAID = 1; // unpaid day off
    public const DAY_TYPE_LEAVE_PAID = 2; // paid leave
    public const DAY_TYPE_LEAVE_UNPAID = 3; // unpaid leave
    public const DAY_TYPE_LEAVE_REQUEST = 4; // leave on request
    public const DAY_TYPE_LEAVE_OCCASIONAL = 5; // occasional leave
    public const DAY_TYPE_LEAVE_CARE = 6; // caring leave
    public const DAY_TYPE_SICK_LEAVE_PAID = 7; // paid sick day
    public const DAY_TYPE_SICK_LEAVE_UNPAID = 8; // unpaid sick day
    public const DAY_TYPE_OVERTIME = 9; // day of collecting overtime (for CoE)
    public const DAY_TYPE_ADDITIONAL_HOURS = 10; // day of receiving additional days (for B2B LUMP SUM and CLC LUMP SUM)
    public const DAY_TYPE_UNAVAILABILITY = 11; // day of unavailability (for B2B HOURLY and CLC HOURLY)

    public const PLANNER_LEAVE_TYPES = [
        self::DAY_TYPE_FREE_PAID            => "day-off",
        self::DAY_TYPE_FREE_UNPAID          => "day-off",
        self::DAY_TYPE_LEAVE_PAID           => "day-off",
        self::DAY_TYPE_LEAVE_UNPAID         => "day-off",
        self::DAY_TYPE_LEAVE_REQUEST        => "day-off",
        self::DAY_TYPE_LEAVE_OCCASIONAL     => "day-off",
        self::DAY_TYPE_LEAVE_CARE           => "day-off",
        self::DAY_TYPE_SICK_LEAVE_PAID      => "sick-leave",
        self::DAY_TYPE_SICK_LEAVE_UNPAID    => "sick-leave",
        self::DAY_TYPE_OVERTIME             => "day-off",
        self::DAY_TYPE_ADDITIONAL_HOURS     => "day-off",
        self::DAY_TYPE_UNAVAILABILITY       => "day-off",
    ];

    public const AVAZA_STATUS_NOT_SYNCED = 1;
    public const AVAZA_STATUS_SYNCED = 2;
    public const AVAZA_STATUS_FAILED = 3;

    /** @Assert\NotBlank @ORM\Column(name="date", type="date", nullable=false) */
    private \DateTime $date;

    /** @Assert\NotBlank @ORM\Column(name="type", type="integer", nullable=false) */
    private int $type = 0;

    /** @Assert\NotBlank @ORM\Column(name="status", type="integer", nullable=false) */
    private int $status = 0;

    /**
     * @Assert\NotBlank
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\LeaveRequest", inversedBy="requestDays")
     * @ORM\JoinColumn(name="request_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private LeaveRequest $request;

    /**
     * @Assert\NotBlank()
     *
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\LeavePeriod")
     * @ORM\JoinColumn(name="period_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private LeavePeriod $period;

    /**
     * @Assert\NotBlank()
     *
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Employee")
     * @ORM\JoinColumn(name="employee_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private Employee $employee;

    /** @ORM\Column(name="hours", type="integer", nullable=true) */
    private ?int $hours;
    /** @ORM\Column(name="avaza_sync_status", type="integer") */
    private int $avazaSyncStatus = self::AVAZA_STATUS_NOT_SYNCED;
    /** @ORM\Column(name="avaza_id", type="string", length=16, nullable=true) */
    private ?string $avazaId = null;

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getEmployee(): ?Employee
    {
        $period = $this->getPeriod();
        if (is_null($period)) {
            return null;
        }
        return $period->getEmployee();
    }

    public function getPeriod() : ?LeavePeriod
    {
        $request = $this->getRequest();
        if (is_null($request)) {
            return null;
        }
        return $request->getPeriod();
    }

    public function getPlannerDayType() : string
    {
        return self::PLANNER_LEAVE_TYPES[$this->getType()] ?? 'N/A';
    }

    public function getRequest() : ?LeaveRequest
    {
        return $this->request;
    }

    public function setRequest(?LeaveRequest $request) : self
    {
        $this->request = $request;
        if (!is_null($request)) {
            $this->period = $request->getPeriod();
            $this->employee = $request->getPeriod()->getEmployee();
        }
        return $this;
    }

    public function getHours(): ?int
    {
        return $this->hours;
    }

    public function setHours(int $hours): self
    {
        $this->hours = $hours;
        return $this;
    }

    public function requiresAcceptance() : bool
    {
        $typesNotRequiringAcceptance = [
            self::DAY_TYPE_OVERTIME,
            self::DAY_TYPE_ADDITIONAL_HOURS,
        ];
        return !in_array($this->getType(), $typesNotRequiringAcceptance);
    }

    public function getAvazaSyncStatus(): int
    {
        return $this->avazaSyncStatus;
    }

    public function setAvazaSyncStatus(int $avazaSyncStatus): LeaveRequestDay
    {
        $this->avazaSyncStatus = $avazaSyncStatus;
        return $this;
    }

    public function getAvazaId(): ?string
    {
        return $this->avazaId;
    }

    public function setAvazaId(?string $avazaId): LeaveRequestDay
    {
        $this->avazaId = $avazaId;
        return $this;
    }
}
