<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 13.02.19
 * Time: 13:38
 */

namespace Divante\Bundle\AdventureBundle\Message\Leaves;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\LeavePeriod;
use Divante\Bundle\AdventureBundle\Entity\LeaveRequestDay;
use Divante\Bundle\AdventureBundle\Entity\SickLeaveAttachment;
use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;

class CreateRequest
{
    use ObjectTrait;

    private LeavePeriod $period;
    private Employee $manager;
    private ?string $comment;
    private int $status;
    /** @var array<int,array<string,mixed>> */
    private array $days;
    /** @var SickLeaveAttachment[] */
    private array $attachments;
    private int $employeeId;

    /**
     * CreateRequest constructor.
     * @param int $periodId
     * @param int $managerId
     * @param array<int,array<string,mixed>> $days
     * @param int[] $attachmentIds
     * @param string|null $comment
     * @param int $status
     * @param int $employeeId
     * @param ObjectManager $em
     * @throws Exception
     */
    public function __construct(
        int $periodId,
        int $managerId,
        array $days,
        array $attachmentIds,
        ?string $comment,
        int $status,
        ObjectManager $em,
        int $employeeId
    ) {
        $this->comment = $comment;
        $this->employeeId = $employeeId;
        $this->status = $status;
        /** @var LeavePeriod|null $period */
        $period = $em->getRepository(LeavePeriod::class)->find($periodId);
        if (is_null($period)) {
            throw new Exception("Period with ID $periodId not found.");
        }
        $this->period = $period;

        /** @var Employee|null $manager */
        $manager = $em->getRepository(Employee::class)->find($managerId);
        if (is_null($manager)) {
            throw new Exception("Manager with ID $managerId not found.");
        }
        $this->manager = $manager;
        if (!$this->manager->isManager()) {
            throw new Exception("Employee with ID $managerId is not a manager.");
        }
        foreach ($days as $day) {
            if (!is_array($day)) {
                throw new Exception("days doesn't contain a correct object entry");
            }
            if (!key_exists('type', $day) || !is_int($day['type'])) {
                throw new Exception("Every day should have an integer 'type' field");
            }
            if (!key_exists($day['type'], LeaveRequestDay::PLANNER_LEAVE_TYPES)) {
                throw new Exception("Type ${day['type']} is not a recognized type of leave request day");
            }
            if (!key_exists('date', $day) || !is_string($day['date'])) {
                throw new Exception("Every day should have an string 'date' field");
            }
            if (!key_exists('hour', $day) || !is_int($day['hour'])) {
                throw new Exception("Every day should have an integer 'hour' field");
            }
        }
        $this->attachments = [];
        $attachRepo = $em->getRepository(SickLeaveAttachment::class);
        /** @var int $attachmentId */
        foreach ($attachmentIds as $attachmentId) {
            /** @var SickLeaveAttachment|null $attachment */
            $attachment = $attachRepo->find($attachmentId);
            if (is_null($attachment)) {
                throw new Exception(sprintf("Attachment with ID %d does not exists.", $attachmentId));
            }
            $this->attachments[] = $attachment;
        }
        $this->days = $days;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    /** @return array<int,array<string,mixed>> */
    public function getDaysData() : array
    {
        return $this->days;
    }


    public function getPeriod() : LeavePeriod
    {
        return $this->period;
    }

    public function getManager() : Employee
    {
        return $this->manager;
    }

    /** @return SickLeaveAttachment[] */
    public function getAttachments() : array
    {
        return $this->attachments;
    }

    public function getEmployeeId(): int
    {
        return $this->employeeId;
    }
}
