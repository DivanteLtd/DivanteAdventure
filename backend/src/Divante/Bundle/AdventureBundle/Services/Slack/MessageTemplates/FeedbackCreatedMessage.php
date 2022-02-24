<?php

namespace Divante\Bundle\AdventureBundle\Services\Slack\MessageTemplates;

use Divante\Bundle\AdventureBundle\Entity\Feedback\Feedback;
use Divante\Bundle\AdventureBundle\Infrastructure\Config\FrontendUrlSupplier;
use Divante\Bundle\AdventureBundle\Services\Slack\Api\SlackSender;
use Divante\Bundle\AdventureBundle\Services\Slack\SlackMessage;
use Divante\Bundle\AdventureBundle\Services\Slack\SlackSectionBlock;
use Divante\Bundle\AdventureBundle\Services\Slack\SlackUrlButton;
use Symfony\Component\Translation\TranslatorInterface;

class FeedbackCreatedMessage
{
    private TranslatorInterface $translator;
    private SlackSender $sender;
    private FrontendUrlSupplier $urlSupplier;

    public function __construct(TranslatorInterface $translator, SlackSender $sender, FrontendUrlSupplier $urlSupplier)
    {
        $this->translator = $translator;
        $this->sender = $sender;
        $this->urlSupplier = $urlSupplier;
    }

    public function sendMessage(Feedback $feedback): void
    {
        $this->translator->setLocale($feedback->getEmployee()->getSlackLanguage());

        $buttonLink = $this->getProfileUrl($feedback);
        $button = new SlackUrlButton($buttonLink, $this->translator->trans('slack.feedback.open_profile'));
        $textMessage = $this->getTextMessage($feedback);
        $message = new SlackMessage($textMessage, [ new SlackSectionBlock($textMessage), $button ]);
        $this->sender->send($message, $feedback->getEmployee());
    }

    private function getProfileUrl(Feedback $feedback): string
    {
        return sprintf(
            "%s/#/firm/employees/%s",
            $this->urlSupplier->getFrontendUrl(),
            $feedback->getEmployee()->getId()
        );
    }

    private function getTextMessage(Feedback $feedback): string
    {
        $leaderName = sprintf(
            "%s %s",
            $feedback->getLeader()->getName(),
            $feedback->getLeader()->getLastName()
        );
        $template = $this->translator->transChoice(
            'slack.feedback.created',
            $feedback->getLeader()->getGender() ?? -1
        );
        return sprintf($template, $leaderName);
    }
}
