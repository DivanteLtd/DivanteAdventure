<?php

namespace Divante\Bundle\AdventureBundle\EventSubscriber;

use Divante\Bundle\AdventureBundle\Events\SlackAdminLogEvent;
use Divante\Bundle\AdventureBundle\Services\Slack\Api\SlackSender;
use Divante\Bundle\AdventureBundle\Services\Slack\SlackAdminReceiver;
use Divante\Bundle\AdventureBundle\Services\Slack\SlackMessage;
use Divante\Bundle\AdventureBundle\Services\Slack\SlackSectionBlock;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SlackAdminLogSubscriber implements EventSubscriberInterface
{
    private SlackSender $sender;
    private SlackAdminReceiver $receiver;

    public function __construct(SlackSender $sender, SlackAdminReceiver $receiver)
    {
        $this->sender = $sender;
        $this->receiver = $receiver;
    }

    /** @return array<string,array<int,array<int,string|int>>> */
    public static function getSubscribedEvents(): array
    {
        return [
            SlackAdminLogEvent::class => [
                [ 'sendLogMessage', 0 ],
            ],
        ];
    }

    public function sendLogMessage(SlackAdminLogEvent $event): void
    {
        $message = new SlackMessage($event->getMessage(), [ new SlackSectionBlock($event->getMessage()) ]);
        $this->sender->send($message, $this->receiver);
    }
}
