<?php

namespace Divante\Bundle\AdventureBundle\Services\Slack\MessageTemplates;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Project;
use Divante\Bundle\AdventureBundle\Entity\Tribe;
use Divante\Bundle\AdventureBundle\Services\Slack\Api\SlackReceiver;
use Divante\Bundle\AdventureBundle\Services\Slack\Api\SlackSender;
use Divante\Bundle\AdventureBundle\Services\Slack\SlackMessage;
use Divante\Bundle\AdventureBundle\Services\Slack\SlackSendableMessage;
use Symfony\Component\Translation\TranslatorInterface;

class IntegrationDisconnectedMessage
{
    /** @var SlackReceiver */
    private $receiver;
    /** @var Employee */
    private $responsible;
    /** @var SlackSender */
    private SlackSender $sender;
    private TranslatorInterface $translator;

    public function __construct(SlackSender $sender, TranslatorInterface $translator)
    {
        $this->sender = $sender;
        $this->translator = $translator;
    }

    public function setReceiver(SlackReceiver $receiver) : self
    {
        $this->receiver = $receiver;
        return $this;
    }

    public function setResponsible(Employee $responsible) : self
    {
        $this->responsible = $responsible;
        return $this;
    }

    public function send() : void
    {
        $message = $this->getMessage();
        $this->sender->send($message, $this->receiver);
    }

    public function getMessage() : SlackSendableMessage
    {
        $this->translator->setLocale($this->receiver->getSlackLanguage());
        $nameKey = $this->getNameKey();
        $receiverName = sprintf($nameKey, $this->receiver->getName());
        $responsibleName = "{$this->responsible->getName()} {$this->responsible->getLastName()}";
        $message = sprintf(
            $this->translator->trans('slack.integration.disconnectMessage'),
            $responsibleName,
            $receiverName
        );
        return new SlackMessage($message);
    }

    private function getNameKey() : string
    {
        if ($this->receiver instanceof Project) {
            return $this->translator->trans('slack.integration.projectTemplate');
        } elseif ($this->receiver instanceof Tribe && $this->receiver->isVirtual()) {
            return $this->translator->trans('slack.integration.departmentTemplate');
        } elseif ($this->receiver instanceof Tribe) {
            return $this->translator->trans('slack.integration.tribeTemplate');
        } else {
            return $this->translator->trans('slack.integration.genericTemplate');
        }
    }
}
