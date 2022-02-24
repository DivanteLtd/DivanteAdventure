<?php

namespace Divante\Bundle\AdventureBundle\Services\Slack\MessageTemplates;

use Divante\Bundle\AdventureBundle\Entity\LeaveRequest;
use Divante\Bundle\AdventureBundle\Entity\LeaveRequestDay;

class NewRequestMessage extends AbstractRequestMessage
{

    protected function addLinkToRequests(LeaveRequest $request): bool
    {
        if ($this->isOvertime($request) || $this->isAdditionalHours($request)) {
            return false;
        }
        return true;
    }

    protected function createMessage(LeaveRequest $request): string
    {
        $employee = $request->getEmployee();
        $employeeName = sprintf("%s %s", $employee->getName(), $employee->getLastName());
        $template = $this->getTranslator()->transChoice(
            'slack.requests.message.createdNewLeaveRequest',
            $employee->getGender() ?? -1,
        );
        if ($this->isAdditionalHours($request)) {
            $template = $this->getTranslator()->transChoice(
                'slack.requests.message.registeredAdditionalHours',
                $employee->getGender() ?? -1,
            );
        }
        if ($this->isOvertime($request)) {
            $template = $this->getTranslator()->transChoice(
                'slack.requests.message.registeredOvertime',
                $employee->getGender() ?? -1,
            );
        }
        return sprintf($template, $employeeName);
    }

    protected function getNotificationMessage(LeaveRequest $request): string
    {
        $employee = $request->getEmployee();
        $employeeName = sprintf("%s %s", $employee->getName(), $employee->getLastName());
        $template = $this->getTranslator()->transChoice(
            'slack.requests.notification.createdNewLeaveRequest',
            $employee->getGender() ?? -1,
        );
        if ($this->isAdditionalHours($request)) {
            $template = $this->getTranslator()->transChoice(
                'slack.requests.notification.registeredAdditionalHours',
                $employee->getGender() ?? -1,
            );
        }
        if ($this->isOvertime($request)) {
            $template = $this->getTranslator()->transChoice(
                'slack.requests.notification.registeredOvertime',
                $employee->getGender() ?? -1,
            );
        }
        return sprintf($template, $employeeName);
    }

    private function isOvertime(LeaveRequest $request) : bool
    {
        if ($request->getStatus() === LeaveRequest::REQUEST_STATUS_ACCEPTED) {
            /** @var LeaveRequestDay $firstDay */
            $firstDay = $request->getRequestDays()->get(0);
            return $firstDay->getType() === LeaveRequestDay::DAY_TYPE_OVERTIME;
        }
        return false;
    }

    private function isAdditionalHours(LeaveRequest $request) : bool
    {
        if ($request->getStatus() === LeaveRequest::REQUEST_STATUS_ACCEPTED) {
            /** @var LeaveRequestDay $firstDay */
            $firstDay = $request->getRequestDays()->get(0);
            return $firstDay->getType() === LeaveRequestDay::DAY_TYPE_ADDITIONAL_HOURS;
        }
        return false;
    }
}
