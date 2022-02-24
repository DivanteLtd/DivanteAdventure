<?php

namespace Divante\Bundle\AdventureBundle\Representation\Email;

use Divante\Bundle\AdventureBundle\Infrastructure\Config\EmailConfig;
use Swift_Attachment;
use Swift_Mailer;
use Twig\Environment;

class EmailConfiguration implements EmailSenderInterface
{
    protected Environment $twig;
    protected Swift_Mailer $mailer;
    protected string $mailerFromAddress;
    protected string $mailerBokAddress;
    protected string $mailerAccountantAddress;

    public function __construct(Environment $twig, Swift_Mailer $mailer, EmailConfig $emailConfig)
    {
        $this->twig = $twig;
        $this->mailer = $mailer;
        $this->mailerFromAddress = $emailConfig->getFromEmail();
        $this->mailerBokAddress = $emailConfig->getBokEmail();
        $this->mailerAccountantAddress = $emailConfig->getAccountantEmail();
    }

    /**
     * @param string|string[] $to
     * @param string $Cc
     * @param string $subject
     * @param array<string,mixed> $params
     * @param string $template
     * @param null|string $attachmentPath
     *
     * @throws EmailException
     */
    public function sendMessage(
        $to,
        ?string $Cc,
        string $subject,
        array $params,
        string $template,
        string $attachmentPath = null
    ) : int {
        /** @var Swift_Attachment[] $attachments */
        $attachments = [];
        if (!empty($attachmentPath)) {
            /** @var Swift_Attachment $attachment */
            $attachment = Swift_Attachment::fromPath($attachmentPath);
            $attachments[] = $attachments;
        }
        return $this->sendMessageWithAttachments($to, $Cc, $subject, $params, $template, $attachments);
    }

    /** @inheritDoc */
    public function sendMessageWithAttachments(
        $to,
        ?string $Cc,
        string $subject,
        array $params,
        string $template,
        array $attachments
    ) : int {
        $body = $this->renderTemplate($params, $template);

        $message = (new \Swift_Message())
            ->setSubject($subject)
            ->setFrom($this->mailerFromAddress)
            ->setTo($to)
            ->setBody($body, 'text/html');
        if (!is_null($Cc)) {
            $message->addCc($Cc);
        }
        foreach ($attachments as $attachment) {
            $message->attach($attachment);
        }
        $result = $this->mailer->send($message);
        if ($result === 0) {
            throw new EmailException('Email did not send');
        }
        return $result;
    }

    /**
     * @param array<string,mixed> $params
     * @param string $template
     * @return string
     */
    private function renderTemplate(array $params, string $template) : string
    {
        return $this->twig->render(
            $template,
            $params
        );
    }

    public function getMailerBokAddress() : string
    {
        return $this->mailerBokAddress;
    }

    public function getMailerAccountantAddress() : string
    {
        return $this->mailerAccountantAddress;
    }
}
