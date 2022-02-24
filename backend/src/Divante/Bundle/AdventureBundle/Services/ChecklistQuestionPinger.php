<?php

namespace Divante\Bundle\AdventureBundle\Services;

use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistQuestion;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Services\Slack\Api\SlackSender;
use Divante\Bundle\AdventureBundle\Services\Slack\MessageTemplates\ChecklistQuestionPingMessage;

class ChecklistQuestionPinger
{
    private SlackSender $sender;
    private ChecklistQuestionPingMessage $message;

    public function __construct(SlackSender $sender, ChecklistQuestionPingMessage $message)
    {
        $this->sender = $sender;
        $this->message = $message;
    }

    public function ping(Employee $pinged, Employee $pinging, ChecklistQuestion $question) : void
    {
        $message = $this->message;
        $message->setPinger($pinging);
        $message->setQuestion($question);
        $this->sender->send($message->getMessage($pinged), $pinged);
    }
}
