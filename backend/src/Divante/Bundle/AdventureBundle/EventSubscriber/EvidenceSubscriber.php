<?php

namespace Divante\Bundle\AdventureBundle\EventSubscriber;

use Divante\Bundle\AdventureBundle\Documents\Pdf\EvidencePdfDocument;
use Divante\Bundle\AdventureBundle\Entity\Evidence;
use Divante\Bundle\AdventureBundle\Entity\EvidenceOvertime;
use Divante\Bundle\AdventureBundle\Events\Evidence\EvidenceEvent;
use Divante\Bundle\AdventureBundle\Events\Evidence\OvertimeEvent;
use Divante\Bundle\AdventureBundle\Infrastructure\Config\EmailConfig;
use Divante\Bundle\AdventureBundle\Representation\Email\EmailSenderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\TranslatorInterface;

class EvidenceSubscriber implements EventSubscriberInterface
{
    private EvidencePdfDocument $pdf;
    private string $evidenceEmail;
    protected TranslatorInterface $translator;
    private EmailSenderInterface $mailer;
    private EntityManagerInterface $entityManager;
    private EmailConfig $emailConfig;

    public function __construct(
        EvidencePdfDocument $pdf,
        EmailConfig $emailConfig,
        TranslatorInterface $translator,
        EmailSenderInterface $mailer,
        EntityManagerInterface $entityManager
    ) {
        $this->pdf = $pdf;
        $this->evidenceEmail = $emailConfig->getEvidenceEmail();
        $this->translator = $translator;
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
        $this->emailConfig = $emailConfig;
    }

    /** @return array<string,array<int,array<int,string|int>>> */
    public static function getSubscribedEvents(): array
    {
        return [
            EvidenceEvent::class => [
                [ 'onEvidence', 0 ],
            ],
            OvertimeEvent::class => [
                [ 'onOvertime', 0 ],
            ],
        ];
    }

    public function onEvidence(EvidenceEvent $event): void
    {
        $employeeLanguage = $event->getEvidence()->getEmployee()->getLanguage();
        $employeeEmail = [$event->getEvidence()->getEmployee()->getEmail()];
        $this->translator->setLocale($employeeLanguage);
        $this->pdf->setEvidence($event->getEvidence());
        $this->pdf->buildAndSendPdf(
            array_merge(
                $employeeEmail,
                $this->emailConfig->getEvidenceEmailsByCompanyBranchAndContractType(
                    $event->getEvidence()->getEmployee()->getCompanyBranch(),
                    $event->getEvidence()->getEmployee()->getContractType()
                )
            )
        );
    }

    public function onOvertime(OvertimeEvent $event): void
    {
        $evidence = $event->getEvidence();
        $language = $evidence->getOvertimeManager()->getLanguage();
        $this->translator->setLocale($language);
        $evidenceId = $evidence->getId();
        /** @var EvidenceOvertime|null $overtimeEntry */
        $overtimeEntry = $this->entityManager->getRepository(EvidenceOvertime::class)
            ->findBy(['evidence' => $evidenceId]);
        $this->mailer->sendMessage(
            $evidence->getOvertimeManager()->getEmail(),
            null,
            $this->createSubject($evidence),
            [
                'evidence' => $evidence,
                'entries' => $overtimeEntry
            ],
            '@Adventure/Emails/overtime_request_new.html.twig'
        );
    }

    private function createSubject(Evidence $evidence) : string
    {
        $employee = $evidence->getEmployee();
        $language = $evidence->getOvertimeManager()->getLanguage();
        $this->translator->setLocale($language);
        return sprintf(
            '[Adventure] %s %s %s',
            $employee->getName(),
            $employee->getLastName(),
            $this->translator->trans('applyForAdditionalHours')
        );
    }
}
