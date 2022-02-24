<?php

namespace Divante\Bundle\AdventureBundle\Services\Slack\MessageTemplates;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Project;
use Divante\Bundle\AdventureBundle\Entity\Tribe;
use Divante\Bundle\AdventureBundle\Services\Slack\Api\SlackReceiver;
use Divante\Bundle\AdventureBundle\Services\Slack\SlackMessage;
use Divante\Bundle\AdventureBundle\Services\Slack\SlackSendableMessage;
use Symfony\Component\Translation\TranslatorInterface;

class WelcomeAfterIntegrationMessage
{
    /** @var SlackReceiver */
    private $receiver;
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function setReceiver(SlackReceiver $receiver) : self
    {
        $this->receiver = $receiver;
        return $this;
    }

    public function getMessage() : SlackSendableMessage
    {
        $this->translator->setLocale($this->receiver->getSlackLanguage());
        $name = $this->receiver->getName();
        $hello = sprintf($this->getHello(), $name);
        $message = sprintf(
            $this->translator->transChoice(
                'slack.integration.connectMessage',
                $this->receiver instanceof Employee ? 0 : 1
            ),
            $hello
        );
        return new SlackMessage($message);
    }

    private function getHello() : string
    {
        if ($this->receiver instanceof Project) {
            return $this->translator->trans('slack.integration.helloProject');
        } elseif ($this->receiver instanceof Tribe && $this->receiver->isVirtual()) {
            return $this->translator->trans('slack.integration.helloDepartment');
        } elseif ($this->receiver instanceof Tribe) {
            return $this->translator->trans('slack.integration.helloTribe');
        } else {
            return $this->translator->trans('slack.integration.helloGeneric');
        }
    }
}
