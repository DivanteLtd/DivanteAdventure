<?php

namespace Divante\Bundle\AdventureBundle\Services\Slack\MessageTemplates;

use Divante\Bundle\AdventureBundle\Entity\LeaveRequest;

class ResignationRequestedMessage extends AbstractRequestMessage
{

    protected function createMessage(LeaveRequest $request): string
    {
        $employee = $request->getEmployee();
        $employeeName = sprintf("%s %s", $employee->getName(), $employee->getLastName());
        return sprintf(
            $this->getTranslator()->transChoice(
                "slack.requests.message.resignationRequested",
                $employee->getGender() ?? -1,
            ),
            $employeeName
        );
    }

    protected function addLinkToRequests(LeaveRequest $request): bool
    {
        return true;
    }

    protected function getNotificationMessage(LeaveRequest $request): string
    {
        $employee = $request->getEmployee();
        $employeeName = sprintf("%s %s", $employee->getName(), $employee->getLastName());
        return sprintf(
            $this->getTranslator()->transChoice(
                "slack.requests.notification.resignationRequested",
                $employee->getGender() ?? -1,
            ),
            $employeeName
        );
    }
}
