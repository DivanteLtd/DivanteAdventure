<?php

namespace Divante\Bundle\AdventureBundle\Services\Slack\MessageTemplates;

use Divante\Bundle\AdventureBundle\Entity\LeaveRequest;

class RequestAcceptedMessage extends AbstractRequestMessage
{

    protected function createMessage(LeaveRequest $request): string
    {
        $manager = $request->getManager();
        $name = sprintf("%s %s", $manager->getName(), $manager->getLastName());
        return sprintf(
            $this->getTranslator()->transChoice(
                'slack.requests.message.requestAccepted',
                $manager->getGender() ?? -1,
            ),
            $name,
        );
    }

    protected function addLinkToRequests(LeaveRequest $request): bool
    {
        return false;
    }

    protected function getNotificationMessage(LeaveRequest $request): string
    {
        $manager = $request->getManager();
        $name = sprintf("%s %s", $manager->getName(), $manager->getLastName());
        return sprintf(
            $this->getTranslator()->transChoice(
                'slack.requests.notification.requestAccepted',
                $manager->getGender() ?? -1,
            ),
            $name,
        );
    }
}
