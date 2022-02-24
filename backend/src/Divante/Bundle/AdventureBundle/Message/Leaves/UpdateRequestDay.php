<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 14.02.19
 * Time: 10:32
 */

namespace Divante\Bundle\AdventureBundle\Message\Leaves;

use Divante\Bundle\AdventureBundle\Entity\LeaveRequestDay;
use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;
use Doctrine\Common\Persistence\ObjectManager;

class UpdateRequestDay
{
    use ObjectTrait;

    private LeaveRequestDay $leaveRequestDay;
    private int $status;
    private int $type;

    /**
     * UpdateRequestDay constructor.
     * @param int $dayId
     * @param int|null $newStatus
     * @param int|null $newType
     * @param ObjectManager $em
     * @throws \Exception
     */
    public function __construct(int $dayId, ?int $newStatus, ?int $newType, ObjectManager $em)
    {
        /** @var LeaveRequestDay|null $leaveRequestDay */
        $leaveRequestDay = $em->getRepository(LeaveRequestDay::class)->find($dayId);
        if (is_null($leaveRequestDay)) {
            throw new \Exception("Leave request day with ID $dayId not found");
        }
        $this->leaveRequestDay = $leaveRequestDay;

        $this->status = $newStatus ?? $this->leaveRequestDay->getStatus();
        $this->type = $newType ?? $this->leaveRequestDay->getType();
    }

    public function getLeaveRequestDay() : LeaveRequestDay
    {
        return $this->leaveRequestDay;
    }

    public function getStatus() : int
    {
        return $this->status;
    }

    public function getType() : int
    {
        return $this->type;
    }
}
