<?php

namespace Divante\Bundle\AdventureBundle\Services\Slack\MessageTemplates;

use Divante\Bundle\AdventureBundle\Entity\LeaveRequest;

class ResignationOvertimeMessage extends AbstractRequestMessage
{

    protected function createMessage(LeaveRequest $request): string
    {
        $employee = $request->getManager();
        $name = sprintf("%s %s", $employee->getName(), $employee->getLastName());
        return sprintf(
            $this->getTranslator()->transChoice(
                "slack.requests.message.resignationOvertime",
                $employee->getGender() ?? -1,
            ),
            $name
        );
    }

    protected function addLinkToRequests(LeaveRequest $request): bool
    {
        return false;
    }

    protected function getNotificationMessage(LeaveRequest $request): string
    {
        $employee = $request->getManager();
        $name = sprintf("%s %s", $employee->getName(), $employee->getLastName());
        return sprintf(
            $this->getTranslator()->transChoice(
                "slack.requests.notification.resignationOvertime",
                $employee->getGender() ?? -1,
            ),
            $name
        );
    }
}
