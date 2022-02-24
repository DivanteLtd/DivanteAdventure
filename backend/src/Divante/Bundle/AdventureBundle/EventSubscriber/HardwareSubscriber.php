<?php

namespace Divante\Bundle\AdventureBundle\EventSubscriber;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Notification;
use Divante\Bundle\AdventureBundle\Events\Hardware\HardwareAssignedEvent;
use Divante\Bundle\AdventureBundle\Events\Hardware\HardwareSignRequestEvent;
use Divante\Bundle\AdventureBundle\Message\Dashboard\CreateNotification;
use Divante\Bundle\AdventureBundle\Representation\Email\EmailSenderInterface;
use Divante\Bundle\AdventureBundle\Infrastructure\Config\FrontendUrlSupplier;
use Divante\Bundle\AdventureBundle\Infrastructure\Config\EmailConfig;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Translation\TranslatorInterface;

class HardwareSubscriber implements EventSubscriberInterface
{
    private EmailSenderInterface $sender;
    private FrontendUrlSupplier $supplier;
    private EmailConfig $emailConfig;
    private MessageBusInterface $messageBus;
    private EntityManagerInterface $em;
    protected TranslatorInterface $translator;

    public function __construct(
        EmailSenderInterface $sender,
        FrontendUrlSupplier $supplier,
        EmailConfig $emailConfig,
        MessageBusInterface $messageBus,
        EntityManagerInterface $em,
        TranslatorInterface $translator
    ) {
        $this->supplier = $supplier;
        $this->sender = $sender;
        $this->emailConfig = $emailConfig;
        $this->messageBus = $messageBus;
        $this->em = $em;
        $this->translator = $translator;
    }

    /** @return array<string,array<int,array<int,string|int>>> */
    public static function getSubscribedEvents() : array
    {
        return [
            HardwareAssignedEvent::class => [
                [ 'newHardwareAssigned', 0 ],
            ],
            HardwareSignRequestEvent::class => [
                [ 'requestSignature', 0 ],
            ],
        ];
    }

    public function newHardwareAssigned(HardwareAssignedEvent $event) : void
    {
        $this->translator->setLocale('en');
        $subject = sprintf(
            '[Adventure] %s (S/N: %s)',
            $this->translator->trans('hardwareMails.generate.part1'),
            $event->getSerialNumber()
        );
        $this->sender->sendMessage(
            $this->emailConfig->getAdmResponsibleEmail(),
            $this->emailConfig->getAdmPersonnelEmail(),
            $subject,
            [
                'firstName' => $event->getFirstName(),
                'lastName' => $event->getLastName(),
                'manufacturer' => $event->getManufacturer(),
                'model' => $event->getModel(),
                'serialNumber' => $event->getSerialNumber(),
                'updatedAt' => $event->getUpdatedAt(),
                'frontendAppUrl' => $this->supplier->getFrontendUrl()
            ],
            'AdventureBundle:Emails:hardware/hardware_generate.html.twig'
        );
    }

    public function requestSignature(HardwareSignRequestEvent $event) : void
    {
        $subject = sprintf(
            '[Adventure] %s',
            $this->translator->trans('hardwareMails.signature.part1')
        );
        $this->sender->sendMessage(
            $event->getEmail(),
            null,
            $subject,
            [
                'password' => $event->getPassword(),
                'agreement' => $event->getAgreement(),
            ],
            'AdventureBundle:Emails:hardware/hardware_signature_request.html.twig'
        );

        /** @var Employee $recipient */
        $recipient = $this->em->getRepository(Employee::class)
            ->findBy(['email' => $event->getEmail()])[0];
        $recipientId = $recipient->getId();
        $description = $event->getAgreement()->getAssignment()->getEmployee()->getId() === $recipientId
            ? Notification::HARDWARE_TO_SIGN : Notification::HARDWARE_TO_SIGN_FOR;
        $subject = $event->getAgreement()->getAssignment()->getEmployee()->getId() === $recipientId ? '' : sprintf(
            '%s %s',
            $event->getAgreement()->getAssignment()->getEmployee()->getName(),
            $event->getAgreement()->getAssignment()->getEmployee()->getLastName(),
        );
        $createEntry = new CreateNotification(
            $recipientId,
            $description,
            $subject,
            '/dashboard'
        );
        $this->messageBus->dispatch($createEntry);
    }
}
