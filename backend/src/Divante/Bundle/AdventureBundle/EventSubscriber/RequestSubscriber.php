<?php

namespace Divante\Bundle\AdventureBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\KernelInterface;

class RequestSubscriber implements EventSubscriberInterface
{
    private KernelInterface $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /** @return array<string,array<int,array<int,string|int>>> */
    public static function getSubscribedEvents() : array
    {
        return [
            KernelEvents::REQUEST => [
                [ 'onKernelRequest', 0 ],
                [ 'checkMaintenance', 10 ],
            ],
        ];
    }

    public function onKernelRequest(GetResponseEvent $event) : void
    {
        if (!$event->isMasterRequest()) {
            return;
        }
        $event->getRequest()->setLocale($event->getRequest()->headers->get('adventurelanguage', 'pl'));
    }

    public function checkMaintenance(GetResponseEvent $event): void
    {
        $rootDir = $this->kernel->getRootDir();
        $projectDir = dirname($rootDir);
        $maintenance = "$projectDir/web/maintenance.flag";
        if (file_exists($maintenance)) {
            $event->setResponse(new Response("", Response::HTTP_SERVICE_UNAVAILABLE));
        }
    }
}
