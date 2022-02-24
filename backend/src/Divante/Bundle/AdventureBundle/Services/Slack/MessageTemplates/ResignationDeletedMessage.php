<?php

namespace Divante\Bundle\AdventureBundle\Services\Slack\MessageTemplates;

use Divante\Bundle\AdventureBundle\Entity\LeaveRequest;

class ResignationDeletedMessage extends AbstractRequestMessage
{

    protected function createMessage(LeaveRequest $request): string
    {
        $employee = $request->getEmployee();
        $employeeName = sprintf("%s %s", $employee->getName(), $employee->getLastName());
        return sprintf(
            $this->getTranslator()->transChoice(
                "slack.requests.message.resignationDeleted",
                $employee->getGender() ?? -1,
            ),
            $employeeName,
        );
    }

    protected function addLinkToRequests(LeaveRequest $request): bool
    {
        return false;
    }

    protected function getNotificationMessage(LeaveRequest $request): string
    {
        $employee = $request->getEmployee();
        $employeeName = sprintf("%s %s", $employee->getName(), $employee->getLastName());
        return sprintf(
            $this->getTranslator()->transChoice(
                "slack.requests.notification.resignationDeleted",
                $employee->getGender() ?? -1,
            ),
            $employeeName,
        );
    }
}
