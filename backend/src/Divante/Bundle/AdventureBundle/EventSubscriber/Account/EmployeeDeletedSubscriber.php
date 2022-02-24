<?php

namespace Divante\Bundle\AdventureBundle\EventSubscriber\Account;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\EmployeeEndCooperation;
use Divante\Bundle\AdventureBundle\Events\Account\EmployeeDeletedEvent;
use Divante\Bundle\AdventureBundle\Representation\Email\EmailConfiguration;
use Divante\Bundle\AdventureBundle\Representation\Email\EmailException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\TranslatorInterface;

class EmployeeDeletedSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $em;
    private EmailConfiguration $mailer;
    private string $notificationAddress;
    private TranslatorInterface $translator;


    public function __construct(EntityManagerInterface $em, EmailConfiguration $mailer, TranslatorInterface $translator)
    {
        $this->em = $em;
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->notificationAddress = $mailer->getMailerBokAddress();
    }

    /** @return array<string,array<int,array<int,string|int>>> */
    public static function getSubscribedEvents() : array
    {
        return [
            EmployeeDeletedEvent::class => [
                [ 'createResignation', 0 ],
                [ 'sendNotification', 10 ],
            ]
        ];
    }

    /**
     * @param EmployeeDeletedEvent $event
     * @throws EmailException
     */
    public function sendNotification(EmployeeDeletedEvent $event) : void
    {
        $this->translator->setLocale('en');
        $this->mailer->sendMessage(
            $this->notificationAddress,
            null,
            $this->createSubject($event->getRemoved()),
            [
                'removed' => $event->getRemoved(),
                'removing' => $event->getRemoving()
            ],
            '@Adventure/Emails/user_deleted_adm_notification.html.twig'
        );
    }

    private function createSubject(Employee $target) : string
    {
        return sprintf(
            '[Adventure] %s %s %s %s',
            $this->translator->trans('Person'),
            $this->translator->trans('hasBeenRemovedFemale'),
            $target->getName(),
            $target->getLastName()
        );
    }

    public function createResignation(EmployeeDeletedEvent $event) : void
    {
        $employee = $event->getRemoved();
        $resignation = new EmployeeEndCooperation();
        $resignation
            ->setEmployee($employee)
            ->setName($employee->getName())
            ->setLastName($employee->getLastName())
            ->setChecklist(false)
            ->setExitInterview(false)
            ->setDismissDate($employee->getHiredTo())
            ->setPosition($this->getPositionName($employee))
            ->setWhoEndedCooperation(EmployeeEndCooperation::ENDED_BY_COMPANY)
            ->setCreatedAt()
            ->setUpdatedAt();
        $this->em->persist($resignation);
    }

    private function getPositionName(Employee $employee) : ?string
    {
        $position = $employee->getPosition();
        return is_null($position) ? null : $position->getName();
    }
}
