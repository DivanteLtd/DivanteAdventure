<?php

namespace Divante\Bundle\AdventureBundle\Services\Slack;

use Divante\Bundle\AdventureBundle\Entity\LeaveRequest;
use Divante\Bundle\AdventureBundle\Entity\LeaveRequestDay;
use Divante\Bundle\AdventureBundle\Events\RequestStatusEvent;
use Divante\Bundle\AdventureBundle\Services\Slack\MessageTemplates\AbstractRequestMessage;
use Divante\Bundle\AdventureBundle\Services\Slack\MessageTemplates\RequestAcceptedMessage;
use Divante\Bundle\AdventureBundle\Services\Slack\MessageTemplates\RequestRejectedMessage;
use Divante\Bundle\AdventureBundle\Services\Slack\MessageTemplates\RequestResignedMessage;
use Divante\Bundle\AdventureBundle\Services\Slack\MessageTemplates\ResignationAcceptedMessage;
use Divante\Bundle\AdventureBundle\Services\Slack\MessageTemplates\ResignationDeletedMessage;
use Divante\Bundle\AdventureBundle\Services\Slack\MessageTemplates\ResignationRejectedMessage;
use Divante\Bundle\AdventureBundle\Services\Slack\MessageTemplates\ResignationRequestedMessage;
use Divante\Bundle\AdventureBundle\Services\Slack\MessageTemplates\ResignationOvertimeMessage;

class RequestMessageFactory
{
    private AbstractRequestMessage$requestAccepted;
    private AbstractRequestMessage$requestRejected;
    private AbstractRequestMessage $requestResigned;
    private AbstractRequestMessage $resignationAccepted;
    private AbstractRequestMessage $resignationDeleted;
    private AbstractRequestMessage $resignationRejected;
    private AbstractRequestMessage $resignationRequested;
    private AbstractRequestMessage $resignationOvertime;

    public function __construct(
        RequestAcceptedMessage $requestAccepted,
        RequestRejectedMessage $requestRejected,
        RequestResignedMessage $requestResigned,
        ResignationAcceptedMessage $resignationAccepted,
        ResignationDeletedMessage $resignationDeleted,
        ResignationRejectedMessage $resignationRejected,
        ResignationRequestedMessage $resignationRequested,
        ResignationOvertimeMessage $resignationOvertime
    ) {
        $this->requestAccepted = $requestAccepted;
        $this->requestRejected = $requestRejected;
        $this->requestResigned = $requestResigned;
        $this->resignationAccepted = $resignationAccepted;
        $this->resignationDeleted = $resignationDeleted;
        $this->resignationRejected = $resignationRejected;
        $this->resignationRequested = $resignationRequested;
        $this->resignationOvertime = $resignationOvertime;
    }

    public function getTemplate(RequestStatusEvent $event) : ?AbstractRequestMessage
    {
        $newStatus = $event->getRequest()->getStatus();
        $oldStatus = $event->getPreviousStatus();
        $changedByManager = $event->changedByManager();
        $changedByEmployee = $event->changedByEmployee();

        /** @var LeaveRequestDay $requestDay */
        $requestDay = $event->getRequest()->getRequestDays()->get(0);
        $type = $requestDay->getType();

        if ($type === LeaveRequestDay::DAY_TYPE_OVERTIME || $type === LeaveRequestDay::DAY_TYPE_ADDITIONAL_HOURS) {
            // ACCEPTED -> ACCEPTED : request resign from single day(s) of receiving overtime
            if ($oldStatus === LeaveRequest::REQUEST_STATUS_ACCEPTED
                && $newStatus === LeaveRequest::REQUEST_STATUS_ACCEPTED) {
                return $this->resignationOvertime;
            }
            // ACCEPTED -> RESIGNED : request resign from receiving overtime
            if ($oldStatus === LeaveRequest::REQUEST_STATUS_ACCEPTED
                && $newStatus === LeaveRequest::REQUEST_STATUS_RESIGNED) {
                return $this->resignationOvertime;
            }
        }

        // PENDING -> ACCEPTED by manager: request accepted
        if ($oldStatus === LeaveRequest::REQUEST_STATUS_PENDING
            && $newStatus === LeaveRequest::REQUEST_STATUS_ACCEPTED
            && $changedByManager) {
            return $this->requestAccepted;
        }
        // PENDING -> REJECTED by manager: request rejected
        if ($oldStatus === LeaveRequest::REQUEST_STATUS_PENDING
            && $newStatus === LeaveRequest::REQUEST_STATUS_REJECTED
            && $changedByManager) {
            return $this->requestRejected;
        }
        // PENDING -> RESIGNED by employee: resigned from request
        if ($oldStatus === LeaveRequest::REQUEST_STATUS_PENDING
            && $newStatus === LeaveRequest::REQUEST_STATUS_RESIGNED
            && $changedByEmployee) {
            return $this->requestResigned;
        }
        // PENDING RESIGNATION -> RESIGNED by manager: resignation accepted
        if ($oldStatus === LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION
            && $newStatus === LeaveRequest::REQUEST_STATUS_RESIGNED
            && $changedByManager) {
            return $this->resignationAccepted;
        }
        // PENDING RESIGNATION -> ACCEPTED by employee: resignation deleted
        if ($oldStatus === LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION
            && $newStatus === LeaveRequest::REQUEST_STATUS_ACCEPTED
            && $changedByEmployee) {
            return $this->resignationDeleted;
        }
        // PENDING RESIGNATION -> ACCEPTED by manager: resignation rejected
        if ($oldStatus === LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION
            && $newStatus === LeaveRequest::REQUEST_STATUS_ACCEPTED
            && $changedByManager) {
            return $this->resignationRejected;
        }
        // ACCEPTED -> PENDING RESIGNATION by employee: resignation requested
        if ($oldStatus === LeaveRequest::REQUEST_STATUS_ACCEPTED
            && $newStatus === LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION
            && $changedByEmployee) {
            return $this->resignationRequested;
        }
        // PLANNED -> ACCEPTED by manager: request accepted
        if ($oldStatus === LeaveRequest::REQUEST_STATUS_PLANNED
            && $newStatus === LeaveRequest::REQUEST_STATUS_ACCEPTED
            && $changedByManager) {
            return $this->requestAccepted;
        }
        // PLANNED -> REJECTED by manager: request rejected
        if ($oldStatus === LeaveRequest::REQUEST_STATUS_PLANNED
            && $newStatus === LeaveRequest::REQUEST_STATUS_REJECTED
            && $changedByManager) {
            return $this->requestRejected;
        }
        return null;
    }
}
