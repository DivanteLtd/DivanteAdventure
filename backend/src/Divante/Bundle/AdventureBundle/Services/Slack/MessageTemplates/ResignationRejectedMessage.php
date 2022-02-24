<?php

namespace Divante\Bundle\AdventureBundle\Services\Slack\MessageTemplates;

use Divante\Bundle\AdventureBundle\Entity\LeaveRequest;

class ResignationRejectedMessage extends AbstractRequestMessage
{

    protected function createMessage(LeaveRequest $request): string
    {
        $employee = $request->getManager();
        $name = sprintf("%s %s", $employee->getName(), $employee->getLastName());
        return sprintf(
            $this->getTranslator()->transChoice(
                "slack.requests.message.resignationRejected",
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
                "slack.requests.notification.resignationRejected",
                $employee->getGender() ?? -1,
            ),
            $name
        );
    }
}
