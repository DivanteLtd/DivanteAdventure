<?php

namespace Divante\Bundle\AdventureBundle\EventSubscriber;

use Divante\Bundle\AdventureBundle\Services\Slack\Api\SlackSender;
use Divante\Bundle\AdventureBundle\Services\Slack\SlackAdminReceiver;
use Divante\Bundle\AdventureBundle\Services\Slack\SlackMessage;
use Divante\Bundle\AdventureBundle\Services\Slack\SlackSectionBlock;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ErrorSubscriber implements EventSubscriberInterface
{
    /** @return array<string,array<int,array<int,string|int>>> */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => [
                [ 'checkException', 0 ],
            ],
            KernelEvents::RESPONSE => [
                [ 'checkResponse', 0 ],
            ],
        ];
    }

    private SlackAdminReceiver $receiver;
    private SlackSender $sender;

    public function __construct(SlackAdminReceiver $receiver, SlackSender $sender)
    {
        $this->receiver = $receiver;
        $this->sender = $sender;
    }

    public function checkResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();
        if ($response->getStatusCode() === Response::HTTP_INTERNAL_SERVER_ERROR) {
            $this->handleMessage((string)$response->getContent());
        }
    }

    public function checkException(ExceptionEvent $event): void
    {
        $response = $event->getResponse();
        $throwable = $event->getThrowable();
        $errorCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        if (!is_null($response) &&
            ($response->isClientError() || $response->isServerError() || $response->isRedirect())) {
            $errorCode = $response->getStatusCode();
        }
        if ($throwable instanceof HttpExceptionInterface) {
            $errorCode = $throwable->getStatusCode();
        }
        if ($errorCode === Response::HTTP_INTERNAL_SERVER_ERROR) {
            $this->handleMessage((string)$throwable);
        }
    }

    private function handleMessage(string $message): void
    {
        try {
            $this->tryHandleMessage($message);
        } catch (\Throwable $thr) {
        }
    }

    private function tryHandleMessage(string $message): void
    {
        $title = "[Adventure] HTTP 500 Error occurred";
        $slackMessage = new SlackMessage($title, [ new SlackSectionBlock($message) ]);
        $this->sender->send($slackMessage, $this->receiver);
    }
}
