<?php

namespace Divante\Bundle\AdventureBundle\Services\Slack\MessageTemplates;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\FAQ\FAQAskedQuestion;
use Divante\Bundle\AdventureBundle\Services\Slack\Api\SlackSender;
use Divante\Bundle\AdventureBundle\Services\Slack\SlackMessage;
use Divante\Bundle\AdventureBundle\Services\Slack\SlackSectionBlock;
use Divante\Bundle\AdventureBundle\Services\Slack\SlackSendableMessage;
use Symfony\Component\Translation\TranslatorInterface;

class FAQResponseMessage
{
    private TranslatorInterface $translator;
    private SlackSender $sender;

    public function __construct(TranslatorInterface $translator, SlackSender $slackSender)
    {
        $this->translator = $translator;
        $this->sender = $slackSender;
    }

    public function send(Employee $sender, FAQAskedQuestion $question, ?string $rejectionMessage) : void
    {
        $message = $this->prepareMessage($sender, $question, $rejectionMessage);
        $this->sender->send($message, $question->getQuestioner());
    }

    private function prepareMessage(
        Employee $sender,
        FAQAskedQuestion $question,
        ?string $rejectionMessage
    ) : SlackSendableMessage {
        $questioner = $question->getQuestioner();
        $senderName = sprintf("%s %s", $sender->getName(), $sender->getLastName());
        $this->translator->setLocale($questioner->getSlackLanguage());
        if (is_null($rejectionMessage)) {
            $template = $this->translator->transChoice('slack.faq.accept', $questioner->getGender() ?? -1);
            $messageText = sprintf($template, $senderName, $question->getQuestion());
        } else {
            $template = $this->translator->transChoice('slack.faq.reject', $questioner->getGender() ?? -1);
            $messageText = sprintf($template, $senderName, $question->getQuestion(), $rejectionMessage);
        }
        return new SlackMessage($messageText, [ new SlackSectionBlock($messageText) ]);
    }
}
