<?php

namespace Divante\Bundle\AdventureBundle\Services;

use Divante\Bundle\AdventureBundle\Entity\LeaveRequest;
use Divante\Bundle\AdventureBundle\Entity\Notification;
use Divante\Bundle\AdventureBundle\Message\Dashboard\CreateNotification;
use Symfony\Component\Messenger\MessageBusInterface;

class RequestNotification
{
    /** @var MessageBusInterface */
    private $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function createNotification(LeaveRequest $request, int $oldStatus, bool $changedByManager) : void
    {
        $status = $request->getStatus();
        $destinationId = $request->getEmployee()->getId();
        $managerId = $request->getManager()->getId();
        $subject = $request->getId();
        $description = null;
        $path = Notification::FREE_DAYS_PATH;

        if ($status === LeaveRequest::REQUEST_STATUS_ACCEPTED && $oldStatus === LeaveRequest::REQUEST_STATUS_PENDING) {
            $description = Notification::USER_REQUEST_ACCEPTED;
        } elseif ($status === LeaveRequest::REQUEST_STATUS_ACCEPTED
            && $oldStatus === LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION && $changedByManager) {
            $description = Notification::USER_RESIGNATION_REQUEST_REJECTED;
        } elseif ($status === LeaveRequest::REQUEST_STATUS_RESIGNED
            && $oldStatus === LeaveRequest::REQUEST_STATUS_PLANNED) {
            $destinationId = $managerId;
            $description = Notification::MANAGER_PLANNED_RESIGN;
            $path = Notification::REQUESTS_PATH;
        } elseif ($status === LeaveRequest::REQUEST_STATUS_ACCEPTED
            && $oldStatus === LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION) {
            $destinationId = $managerId;
            $description = Notification::MANAGER_RESIGN_RESIGNATION;
            $path = Notification::REQUESTS_PATH;
        } elseif ($status === LeaveRequest::REQUEST_STATUS_RESIGNED
            && $oldStatus === LeaveRequest::REQUEST_STATUS_PENDING) {
            $destinationId = $managerId;
            $description = Notification::MANAGER_RESIGN;
            $path = Notification::REQUESTS_PATH;
        } elseif ($status === LeaveRequest::REQUEST_STATUS_REJECTED) {
            $description = Notification::USER_REQUEST_REJECTED;
        } elseif ($status === LeaveRequest::REQUEST_STATUS_RESIGNED
            && $oldStatus === LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION) {
            $description = Notification::USER_RESIGNATION_REQUEST_ACCEPTED;
        } elseif ($status === LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION) {
            $description = Notification::MANAGER_NEW_RESIGN;
            $path = Notification::REQUESTS_PATH;
            $destinationId = $managerId;
            $employee = $request->getEmployee();
            $subject = $employee->getName().' '.$employee->getLastName();
        }

        if (!is_null($description)) {
            $createEntry = new CreateNotification(
                $destinationId,
                $description,
                $subject,
                $path
            );
            $this->messageBus->dispatch($createEntry);
        }
    }
}
