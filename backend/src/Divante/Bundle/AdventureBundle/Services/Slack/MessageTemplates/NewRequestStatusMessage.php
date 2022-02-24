<?php

namespace Divante\Bundle\AdventureBundle\Services\Slack\MessageTemplates;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\EmployeeWorkLocation;
use Divante\Bundle\AdventureBundle\Services\Slack\Api\SlackReceiver;
use Divante\Bundle\AdventureBundle\Services\Slack\Api\SlackSender;
use Divante\Bundle\AdventureBundle\Services\Slack\SlackMessage;
use Symfony\Component\Translation\TranslatorInterface;

class NewRequestStatusMessage
{
    private SlackSender $sender;
    private TranslatorInterface $translator;
    /** @var Employee */
    private $employee;

    public function __construct(SlackSender $sender, TranslatorInterface $translator)
    {
        $this->sender = $sender;
        $this->translator = $translator;
    }

    public function setEmployee(Employee $employee) : self
    {
        $this->employee = $employee;
        return $this;
    }

    public function send(SlackReceiver $receiver, ?int $status = null) : void
    {
        $this->translator->setLocale($receiver->getSlackLanguage());
        $message = sprintf(
            $this->getMessageTemplate($status, $this->employee->getGender() ?? -1),
            $this->employee->getName(),
            $this->employee->getLastName(),
        );
        $this->sender->send(new SlackMessage($message), $receiver);
    }

    private function getMessageTemplate(?int $status, int $gender) : string
    {
        if (is_null($status)) {
            return $this->translator->transChoice('slack.status.dayoffUpdate', $gender);
        }
        switch ($status) {
            case EmployeeWorkLocation::TYPE_REMOTE:
                return $this->translator->transChoice('slack.status.remoteUpdate', $gender);
            case EmployeeWorkLocation::TYPE_DELEGATION:
                return $this->translator->transChoice('slack.status.delegationUpdate', $gender);
        }
        return '';
    }
}
