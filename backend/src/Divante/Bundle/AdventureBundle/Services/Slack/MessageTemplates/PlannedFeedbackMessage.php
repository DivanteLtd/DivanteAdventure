<?php

namespace Divante\Bundle\AdventureBundle\Services\Slack\MessageTemplates;

use Divante\Bundle\AdventureBundle\Entity\Feedback\PlannedFeedback;
use Divante\Bundle\AdventureBundle\Services\Slack\Api\SlackSender;
use Divante\Bundle\AdventureBundle\Services\Slack\SlackMessage;
use Divante\Bundle\AdventureBundle\Services\Slack\SlackSectionBlock;
use Symfony\Component\Translation\TranslatorInterface;

class PlannedFeedbackMessage
{
    private TranslatorInterface $translator;
    private SlackSender $sender;

    public function __construct(TranslatorInterface $translator, SlackSender $sender)
    {
        $this->translator = $translator;
        $this->sender = $sender;
    }

    public function sendMessage(int $days, PlannedFeedback $feedback): void
    {
        $this->translator->setLocale($feedback->getLeader()->getSlackLanguage());
        $employeeName = sprintf(
            "%s %s",
            $feedback->getEmployee()->getName(),
            $feedback->getEmployee()->getLastName()
        );
        $template = $this->translator->trans('slack.feedback.planned_incoming');
        $messageText = sprintf($template, $employeeName, $days);
        $message = new SlackMessage($messageText, [ new SlackSectionBlock($messageText) ]);
        $this->sender->send($message, $feedback->getLeader());
    }
}
