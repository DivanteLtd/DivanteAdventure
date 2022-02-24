<?php

namespace Divante\Bundle\AdventureBundle\EventSubscriber;

use Divante\Bundle\AdventureBundle\Events\AbstractSlackStatusEvent;
use Divante\Bundle\AdventureBundle\Events\RequestStatusEvent;
use Divante\Bundle\AdventureBundle\Events\WorkLocationStatusEvent;
use Divante\Bundle\AdventureBundle\Services\RequestNotification;
use Divante\Bundle\AdventureBundle\Services\Slack\RequestMessageFactory;
use Divante\Bundle\AdventureBundle\Services\Slack\Api\SlackReceiver;
use Divante\Bundle\AdventureBundle\Services\Slack\Api\SlackSender;
use Divante\Bundle\AdventureBundle\Services\Slack\UpdateStatusNotification;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UpdateRequestSlackSubscriber implements EventSubscriberInterface
{
    private SlackSender $sender;
    private RequestNotification $adventureNotification;
    private RequestMessageFactory $messageFactory;
    private UpdateStatusNotification $slackNotification;

    public function __construct(
        SlackSender $sender,
        RequestMessageFactory $messageFactory,
        RequestNotification $adventureNotification,
        UpdateStatusNotification $slackNotification
    ) {
        $this->sender = $sender;
        $this->messageFactory = $messageFactory;
        $this->adventureNotification = $adventureNotification;
        $this->slackNotification = $slackNotification;
    }

    /** @return array<string,array<int,array<int,string|int>>> */
    public static function getSubscribedEvents() : array
    {
        return [
            RequestStatusEvent::class => [
                [ 'sendSlackNotification', 0 ],
                [ 'sendAdventureNotification', 10 ],
                [ 'sendTribeNotification', 20 ],
            ],
            WorkLocationStatusEvent::class => [
                [ 'sendTribeNotification', 0 ],
            ],
        ];
    }

    public function sendAdventureNotification(RequestStatusEvent $event) : void
    {
        $request = $event->getRequest();
        $previous = $event->getPreviousStatus();
        $changedByManager = $event->changedByManager();
        $this->adventureNotification->createNotification($request, $previous, $changedByManager);
    }

    public function sendSlackNotification(RequestStatusEvent $event) : void
    {
        $template = $this->messageFactory->getTemplate($event);
        $receiver = $this->getReceiver($event);
        if (!is_null($template) && !is_null($receiver)) {
            $request = $event->getRequest();
            $message = $template->setRequest($request)->getMessage($receiver);
            $this->sender->send($message, $receiver);
        }
    }

    private function getReceiver(RequestStatusEvent $event) : ?SlackReceiver
    {
        if ($event->changedByManager()) {
            return $event->getRequest()->getEmployee();
        } elseif ($event->changedByEmployee()) {
            return $event->getRequest()->getManager();
        } else {
            return null;
        }
    }

    public function sendTribeNotification(AbstractSlackStatusEvent $event) : void
    {
        if (!$event->isEventAcceptingRequest() || !$event->isRequestContainingToday()) {
            return;
        }
        $this->slackNotification->notify($event->getEmployee(), $event->getTribeNotificationType());
    }
}
