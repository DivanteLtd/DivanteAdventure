<?php

namespace Divante\Bundle\AdventureBundle\Services\Slack\MessageTemplates;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Services\Slack\Api\SlackReceiver;
use Divante\Bundle\AdventureBundle\Services\Slack\Api\SlackSender;
use Divante\Bundle\AdventureBundle\Services\Slack\SlackMessage;
use Divante\Bundle\AdventureBundle\Services\Slack\SlackSectionBlock;
use Divante\Bundle\AdventureBundle\Services\Slack\SlackSendableMessage;
use Symfony\Component\Translation\TranslatorInterface;

class FAQMessage
{
    private Employee $author;
    private string $categoryName;
    private string $question;
    private TranslatorInterface $translator;
    private SlackSender $sender;

    public function __construct(TranslatorInterface $translator, SlackSender $slackSender)
    {
        $this->translator = $translator;
        $this->sender = $slackSender;
    }

    public function setData(
        Employee $author,
        string $categoryName,
        string $question
    ) : self {
        $this->author = $author;
        $this->categoryName = $categoryName;
        $this->question = $question;
        return $this;
    }

    public function sendTo(SlackReceiver $receiver) : void
    {
        $this->sender->send($this->getFAQMessage($receiver), $receiver);
    }

    private function getFAQMessage(SlackReceiver $receiver) : SlackSendableMessage
    {
        $this->translator->setLocale($receiver->getSlackLanguage());
        $template = $this->translator->transChoice(
            'youReceiveAQuestionSlackTemplate',
            $this->author->getGender() ?? -1
        );
        $employeeName = sprintf("%s %s", $this->author->getName(), $this->author->getLastName());
        $messageText = sprintf($template, $employeeName, $this->categoryName, $this->question);
        return new SlackMessage($messageText, [ new SlackSectionBlock($messageText) ]);
    }
}
