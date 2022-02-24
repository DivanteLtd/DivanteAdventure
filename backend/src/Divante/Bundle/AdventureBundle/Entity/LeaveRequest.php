<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 07.01.19
 * Time: 11:06
 */

namespace Divante\Bundle\AdventureBundle\Entity;

use DateTime;
use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Divante\Bundle\AdventureBundle\Entity\Data\Timestampable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class LeaveRequest
 * @package Divante\Bundle\AdventureBundle\Entity
 * @ORM\Table(
 *      name="leave_request",
 *      indexes={
 *          @ORM\Index(name="FK_leave_request_employee_leave_period", columns={"period_id"}),
 *          @ORM\Index(name="FK_leave_request_employee_2", columns={"manager_id"})
 *      }
 * )
 * @ORM\Entity
 */
class LeaveRequest
{
    use Timestampable, Id;

    public const REQUEST_STATUS_PENDING = 0;
    public const REQUEST_STATUS_ACCEPTED = 1;
    public const REQUEST_STATUS_REJECTED = 2;
    public const REQUEST_STATUS_PENDING_RESIGNATION = 3;
    public const REQUEST_STATUS_RESIGNED = 4;
    public const REQUEST_STATUS_PLANNED = 5;

    private const EMPLOYEE = 'employee';
    private const MANAGER = 'manager';

    /**
     * @var array<int,array<string,int|string>> All posible changes of status
     * Every entry has three elements:
     * - src - state from which there is a change
     * - dest - state to which there is a change
     * - role - required role on user ('employee' for person who created a request and 'manager' for person who
     *          accepts request)
     */
    public const LEGAL_STATUS_CHANGES = [
        [ // resigning from created request
            'src' => self::REQUEST_STATUS_PENDING,
            'dest' => self::REQUEST_STATUS_RESIGNED,
            'role' => self::EMPLOYEE,
        ],[ // rejecting request
            'src' => self::REQUEST_STATUS_PENDING,
            'dest' => self::REQUEST_STATUS_REJECTED,
            'role' => self::MANAGER,
        ],[ // accepting request
            'src' => self::REQUEST_STATUS_PENDING,
            'dest' => self::REQUEST_STATUS_ACCEPTED,
            'role' => self::MANAGER,
        ],[ // requesting for resignation
            'src' => self::REQUEST_STATUS_ACCEPTED,
            'dest' => self::REQUEST_STATUS_PENDING_RESIGNATION,
            'role' => self::EMPLOYEE,
        ],[ // resigning from resignation
            'src' => self::REQUEST_STATUS_PENDING_RESIGNATION,
            'dest' => self::REQUEST_STATUS_ACCEPTED,
            'role' => self::EMPLOYEE,
        ],[ // rejecting resignation
            'src' => self::REQUEST_STATUS_PENDING_RESIGNATION,
            'dest' => self::REQUEST_STATUS_ACCEPTED,
            'role' => self::MANAGER,
        ],[ // accepting resignation
            'src' => self::REQUEST_STATUS_PENDING_RESIGNATION,
            'dest' => self::REQUEST_STATUS_RESIGNED,
            'role' => self::MANAGER,
        ],[ // accepting planned request
            'src' => self::REQUEST_STATUS_PLANNED,
            'dest' => self::REQUEST_STATUS_ACCEPTED,
            'role' => self::MANAGER,
        ],[ // rejecting planned request
            'src' => self::REQUEST_STATUS_PLANNED,
            'dest' => self::REQUEST_STATUS_REJECTED,
            'role' => self::MANAGER,
        ],[ // resigning from planned request
            'src' => self::REQUEST_STATUS_PLANNED,
            'dest' => self::REQUEST_STATUS_RESIGNED,
            'role' => self::EMPLOYEE,
        ],[ // resigning from receiving overtime
            'src' => self::REQUEST_STATUS_ACCEPTED,
            'dest' => self::REQUEST_STATUS_PENDING_RESIGNATION,
            'role' => self::MANAGER,
        ]
    ];

    /** @ORM\Column(name="comment", type="text", length=65535, nullable=true) */
    private ?string $comment;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private int $status = 0;

    /** @ORM\Column(name="accepted_at", type="datetime", nullable=true) */
    private ?DateTime $acceptedAt = null;

    /**
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Employee")
     * @ORM\JoinColumn(name="manager_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private ?Employee $manager = null;

    /**
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="LeavePeriod")
     * @ORM\JoinColumn(name="period_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private LeavePeriod  $period;

    /**
     * @var Collection<int,SickLeaveAttachment>
     * @ORM\ManyToMany(targetEntity="SickLeaveAttachment", inversedBy="requests")
     * @ORM\JoinTable(name="sick_leave_request_attachment",
     *      joinColumns={@ORM\JoinColumn(name="sick_leave_request_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="sick_leave_attachment_id", referencedColumnName="id", onDelete="cascade")
     *      }
     * )
     */
    private Collection $attachments;

    /** @Assert\NotBlank @ORM\Column(name="resignation", type="boolean", nullable=false, options={"default":"0"}) */
    private bool $isResigned = false;

    /** @ORM\Column(name="hidden", type="boolean", nullable=false, options={"default":"0"}) */
    private bool $hidden = false;

    /**
     * @var Collection<int,LeaveRequestDay>
     * @ORM\OneToMany(targetEntity="Divante\Bundle\AdventureBundle\Entity\LeaveRequestDay", mappedBy="request")
     */
    private Collection $requestDays;

    public function __construct()
    {
        $this->attachments = new ArrayCollection();
        $this->requestDays = new ArrayCollection();
    }

    public function getComment() : ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment) : self
    {
        $this->comment = $comment;
        return $this;
    }

    public function getStatus() : int
    {
        return $this->status;
    }

    public function setStatus(int $status) : self
    {
        $this->status = $status;
        return $this;
    }

    public function getAcceptedAt() : ?DateTime
    {
        return $this->acceptedAt;
    }

    /**
     * @param string|null $acceptedAt
     * @return LeaveRequest
     * @throws \Exception
     */
    public function setAcceptedAt(?string $acceptedAt = null) : self
    {
        if (!is_null($acceptedAt)) {
            $this->acceptedAt = new DateTime($acceptedAt);
        } elseif ($this->getStatus() == self::REQUEST_STATUS_ACCEPTED
            || $this->getStatus() == self::REQUEST_STATUS_REJECTED
            || $this->getStatus() == self::REQUEST_STATUS_RESIGNED
        ) {
            $this->acceptedAt = new DateTime();
        }

        return $this;
    }

    public function getManager() : ?Employee
    {
        return $this->manager;
    }

    public function setManager(?Employee $manager) : self
    {
        $this->manager = $manager;
        return $this;
    }

    public function getEmployee() : ?Employee
    {
        $period = $this->getPeriod();
        return $period->getEmployee();
    }

    public function setEmployee(?Employee $employee) : self
    {
        $this->getPeriod()->setEmployee($employee);
        return $this;
    }

    public function getPeriod() : LeavePeriod
    {
        return $this->period;
    }

    public function setPeriod(LeavePeriod $period) : self
    {
        $this->period = $period;
        return $this;
    }

    /** @return Collection<int,SickLeaveAttachment> */
    public function getAttachments(): Collection
    {
        return $this->attachments;
    }

    public function addAttachment(SickLeaveAttachment $attachment) : self
    {
        if (!$this->attachments->contains($attachment)) {
            $this->attachments->add($attachment);
        }
        return $this;
    }

    public function isResigned() : bool
    {
        return $this->isResigned;
    }

    public function setResigned(bool $resigned) : self
    {
        $this->isResigned = $resigned;
        return $this;
    }

    /** @return Collection<int,LeaveRequestDay> */
    public function getRequestDays() : Collection
    {
        return $this->requestDays;
    }

    public function isHidden() : bool
    {
        return $this->hidden;
    }

    public function setHidden(bool $hidden) : self
    {
        $this->hidden = $hidden;
        return $this;
    }

    public function canChangeStatus(int $newStatus, Employee $user) : bool
    {
        if ($newStatus === $this->status) {
            return true;
        }
        $tries = [];
        if ($user->getId() === $this->getPeriod()->getEmployee()->getId()) {
            $tries[] = [
                'src' => $this->status,
                'dest' => $newStatus,
                'role' => self::EMPLOYEE
            ];
        }
        if ($user->getId() === $this->getManager()->getId() || $user->getUser()->hasRole('ROLE_SUPER_ADMIN')) {
            $tries[] = [
                'src' => $this->status,
                'dest' => $newStatus,
                'role' => self::MANAGER
            ];
        }

        foreach ($tries as $try) {
            if (in_array($try, self::LEGAL_STATUS_CHANGES)) {
                return true;
            }
        }
        return false;
    }
}
