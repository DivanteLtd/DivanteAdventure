<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Hardware;

use DateTime;
use Divante\Bundle\AdventureBundle\Documents\Pdf\HardwareAgreementPdfDocument;
use Divante\Bundle\AdventureBundle\Entity\Hardware\HardwareAgreement;
use Divante\Bundle\AdventureBundle\Events\Hardware\HardwareSignRequestEvent;
use Divante\Bundle\AdventureBundle\Message\Hardware\SignHardwareAgreement;
use Divante\Bundle\AdventureBundle\Representation\Email\EmailSenderInterface;
use Divante\Bundle\AdventureBundle\Infrastructure\Config\EmailConfig;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Swift_Attachment;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Translation\TranslatorInterface;

class SignHardwareAgreementHandler
{
    private EntityManagerInterface $em;
    private string $helpdeskResponsibleEmail;
    private string $helpdeskEmail;
    private string $admPersonnelEmail;
    private EventDispatcherInterface $eventDispatcher;
    private EmailSenderInterface $mailer;
    private HardwareAgreementPdfDocument $document;
    private TranslatorInterface $translator;

    public function __construct(
        EntityManagerInterface $em,
        EmailConfig $emailConfig,
        EventDispatcherInterface $eventDispatcher,
        HardwareAgreementPdfDocument $document,
        EmailSenderInterface $mailer,
        TranslatorInterface $translator
    ) {
        $this->em = $em;
        $this->helpdeskResponsibleEmail = $emailConfig->getHelpdeskResponsibleEmail();
        $this->helpdeskEmail = $emailConfig->getHelpdeskDepartmentEmail();
        $this->admPersonnelEmail = $emailConfig->getAdmPersonnelEmail();
        $this->eventDispatcher = $eventDispatcher;
        $this->mailer = $mailer;
        $this->document = $document;
        $this->translator = $translator;
    }

    /**
     * @param SignHardwareAgreement $message
     * @throws Exception
     */
    public function __invoke(SignHardwareAgreement $message) : void
    {
        $password = $message->getPassword();
        $agreement = $this->getAgreement($message->getAgreementId(), $password);
        $user = $message->getSigner();
        if (is_null($agreement->getSignedByHelpdesk()) && $user->getEmail() === $this->helpdeskResponsibleEmail) {
            $agreement->setSignedByHelpdesk(new DateTime());
            $agreement->setHelpdeskSigner($user);
        } elseif ($this->isReadyForSigningByUser($agreement)) {
            $assignment = $agreement->getAssignment();
            if (is_null($assignment)) {
                throw new Exception("Agreement doesn't have an assignment");
            }
            $employee = $assignment->getEmployee();
            if (is_null($employee)) {
                throw new Exception("Assignment doesn't have an employee");
            }
            if ($employee->getEmail() !== $user->getEmail()) {
                throw new AccessDeniedHttpException("You are not allowed to sign this agreement");
            } else {
                $agreement->setSignedByUser(new DateTime());
            }
        } else {
            throw new AccessDeniedHttpException("You are not allowed to sign this agreement");
        }
        $this->em->flush();
        if ($this->isReadyForSigningByUser($agreement)) {
            $assignment = $agreement->getAssignment();
            if (is_null($assignment)) {
                throw new Exception("Agreement doesn't have an assignment");
            }
            $employee = $assignment->getEmployee();
            if (is_null($employee)) {
                throw new Exception("Assignment doesn't have an employee");
            }
            $employeeEmail = $employee->getEmail();
            $language = $employee->getLanguage();
            $this->translator->setLocale($language);
            $this->eventDispatcher->dispatch(new HardwareSignRequestEvent($agreement, $password, $employeeEmail));
        }
        if ($agreement->getSignedByUser()) {
            $assignment = $agreement->getAssignment();
            if (is_null($assignment)) {
                throw new Exception("Agreement doesn't have an assignment");
            }
            $employee = $assignment->getEmployee();
            if (is_null($employee)) {
                throw new Exception("Assignment doesn't have an employee");
            }
            $this->translator->setLocale('en');
            $subject = sprintf(
                '[Adventure] %s',
                $this->translator->trans('hardwareMails.info.part1')
            );
            $this->mailer->sendMessage(
                $this->helpdeskEmail,
                null,
                $subject,
                ['assignment' => $assignment],
                'AdventureBundle:Emails:hardware/hardware_info_for_helpdesk.html.twig'
            );
            $renderedView = $this->document->buildPdf($agreement, $password, 'pl');
            $subject = sprintf(
                '[Adventure] %s',
                $this->translator->trans('hardwareMails.ready.part1')
            );
            $this->mailer->sendMessageWithAttachments(
                $this->admPersonnelEmail,
                null,
                $subject,
                [],
                'AdventureBundle:Emails:hardware/hardware_ready_to_archive.html.twig',
                [ new Swift_Attachment($renderedView, "agreement.pdf", "application/pdf") ]
            );
            $employeeEmail = $employee->getEmail();
            $language = $employee->getLanguage();
            $this->translator->setLocale($language);
            $attachments = [ new Swift_Attachment($renderedView, "agreement.pdf", "application/pdf") ];
            if ($employee->getLanguage() === 'en') {
                $renderedViewEn = $this->document->buildPdf($agreement, $password, 'en');
                $attachmentEn = [ new Swift_Attachment($renderedViewEn, "agreementEn.pdf", "application/pdf") ];
                $attachments = array_merge($attachments, $attachmentEn);
            }
            $subject = sprintf(
                '[Adventure] %s: %s %s',
                $this->translator->trans('yourHardwareLendingAgreement'),
                $assignment->getCategory(),
                $assignment->getModel()
            );
            $this->mailer->sendMessageWithAttachments(
                $employeeEmail,
                null,
                $subject,
                [],
                'AdventureBundle:Emails:hardware/hardware_employee_agreement.html.twig',
                $attachments
            );
        }
    }

    private function isReadyForSigningByUser(HardwareAgreement $agreement) : bool
    {
        return is_null($agreement->getSignedByUser()) && !is_null($agreement->getSignedByHelpdesk());
    }

    private function getAgreement(int $id, string $password) : HardwareAgreement
    {
        $repo = $this->em->getRepository(HardwareAgreement::class);
        /** @var HardwareAgreement|null $agreement */
        $agreement = $repo->find($id);
        if (is_null($agreement)) {
            throw new NotFoundHttpException("Agremeent with given ID has not been found");
        }
        if (!$agreement->getGenerationDate()) {
            throw new BadRequestHttpException("Agreement with given ID has not been generated yet.");
        }
        $hashedPassword = $agreement->getPasswordHashed();
        if (is_null($hashedPassword)) {
            throw new BadRequestHttpException("Agreement with given ID does not have a hashed password");
        }
        if (!password_verify($password, $hashedPassword)) {
            throw new AccessDeniedHttpException("Wrong password given for agreement with given ID");
        }
        return $agreement;
    }
}
