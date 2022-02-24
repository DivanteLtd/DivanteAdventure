<?php

namespace Divante\Bundle\AdventureBundle\EventSubscriber\Account;

use Divante\Bundle\AdventureBundle\Events\Account\AccountUnlockEvent;
use Divante\Bundle\AdventureBundle\Events\SlackAdminLogEvent;
use Divante\Bundle\AdventureBundle\Infrastructure\Config\EmailConfig;
use Divante\Bundle\AdventureBundle\Representation\Email\EmailSenderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class AccountUnlockedSubscriber
 *
 * @package Divante\Bundle\AdventureBundle\EventSubscriber\Account
 * @author PK <pk@divante.com>
 */
class AccountUnlockedSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $entityManager;
    private TranslatorInterface $translator;
    private EmailSenderInterface $mailer;
    private EventDispatcherInterface $eventDispatcher;
    private string $techEmail;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        EmailConfig $emailConfig,
        EmailSenderInterface $mailer,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->entityManager = $entityManager;
        $this->translator    = $translator;
        $this->mailer = $mailer;
        $this->eventDispatcher = $eventDispatcher;
        $this->techEmail = $emailConfig->getTechEmail();
    }

    /** @return array<string,array<int,array<int,string|int>>> */
    public static function getSubscribedEvents() :array
    {
        return [
            AccountUnlockEvent::class => [
                [ 'onAccountUnlock', 0 ]
            ],
        ];
    }

    public function onAccountUnlock(AccountUnlockEvent $event) :void
    {
        $employee = $event->getEmployee();
        $language = $employee->getLanguage() ?? 'en';
        $this->translator->setLocale($language);
        $subject = $this->translator->trans('accountUnlocked');
        $this->mailer->sendMessage(
            $employee->getEmail(),
            $this->techEmail,
            $subject,
            ['employee' => $employee],
            'AdventureBundle:Emails:account_unlock_code.html.twig'
        );
        $message = sprintf(
            "*%s %s* - account unlocked",
            $employee->getName(),
            $employee->getLastName(),
        );
        $this->eventDispatcher->dispatch(new SlackAdminLogEvent($message));
    }
}
