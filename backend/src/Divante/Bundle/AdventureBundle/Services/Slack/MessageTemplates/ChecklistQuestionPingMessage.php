<?php

namespace Divante\Bundle\AdventureBundle\Services\Slack\MessageTemplates;

use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistQuestion;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Infrastructure\Config\FrontendUrlSupplier;
use Divante\Bundle\AdventureBundle\Services\Slack\Api\SlackReceiver;
use Divante\Bundle\AdventureBundle\Services\Slack\SlackActionBlock;
use Divante\Bundle\AdventureBundle\Services\Slack\SlackMessage;
use Divante\Bundle\AdventureBundle\Services\Slack\SlackSectionBlock;
use Divante\Bundle\AdventureBundle\Services\Slack\SlackSendableMessage;
use Divante\Bundle\AdventureBundle\Services\Slack\SlackUrlButton;
use Symfony\Component\Translation\TranslatorInterface;

class ChecklistQuestionPingMessage
{
    private ?Employee $pinger = null;
    private ?ChecklistQuestion $question = null;
    private TranslatorInterface $translator;
    private string $frontendUrl;

    public function __construct(TranslatorInterface $translator, FrontendUrlSupplier $frontendUrlSupplier)
    {
        $this->translator = $translator;
        $this->frontendUrl = $frontendUrlSupplier->getFrontendUrl();
    }

    public function setPinger(Employee $pinger) : void
    {
        $this->pinger = $pinger;
    }

    public function setQuestion(ChecklistQuestion $question) : void
    {
        $this->question = $question;
    }

    public function getMessage(SlackReceiver $receiver) : SlackSendableMessage
    {
        $this->translator->setLocale($receiver->getSlackLanguage());
        $message = sprintf(
            $this->translator->trans('slack.ping.message'),
            $this->getPingerName(),
            $this->getQuestionName(),
        );
        return new SlackMessage($message, [
            new SlackSectionBlock($message),
            $this->buildLinkToFrontend(),
        ]);
    }

    private function buildLinkToFrontend() : SlackSendableMessage
    {
        $questionId = $this->question->getId();
        $checklistId = $this->question->getChecklist()->getId();
        $url = sprintf("%s/#/question/update/%s/%s", $this->frontendUrl, $checklistId, $questionId);
        $button = new SlackUrlButton($url, $this->translator->trans('slack.ping.link'));
        return new SlackActionBlock([ $button ]);
    }

    private function getPingerName() : string
    {
        if (is_null($this->pinger)) {
            return '';
        }
        return sprintf("%s %s", $this->pinger->getName(), $this->pinger->getLastName());
    }

    private function getQuestionName() : string
    {
        if (is_null($this->question)) {
            return '';
        }
        if ($this->translator->getLocale() === 'pl') {
            return $this->question->getNamePl();
        }
        return $this->question->getNameEn();
    }
}
