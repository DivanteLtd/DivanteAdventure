<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 22.03.19
 * Time: 10:59
 */

namespace Divante\Bundle\AdventureBundle\Services\RequestMailer;

use Divante\Bundle\AdventureBundle\Infrastructure\Config\EmailConfig;
use Divante\Bundle\AdventureBundle\Representation\Email\EmailConfiguration;
use Divante\Bundle\AdventureBundle\Infrastructure\Config\FrontendUrlSupplier;
use Divante\Bundle\AdventureBundle\Services\RequestMailer\Templates\AbstractTemplate;

class Mailer
{
    private EmailConfiguration $emailSender;
    private string $admEmail;
    private string $accountantEmail;
    private string $frontendUrl;

    public function __construct(
        FrontendUrlSupplier $frontendUrl,
        EmailConfiguration $emailSender,
        EmailConfig $emailConfig
    ) {
        $this->emailSender = $emailSender;
        $this->admEmail = $emailConfig->getBokEmail();
        $this->accountantEmail = $emailConfig->getAccountantEmail();
        $this->frontendUrl = $frontendUrl->getFrontendUrl();
    }

    public function send(AbstractTemplate $template) : void
    {
        $data = $template->getData();
        $employeeEmail = $template->getEmployeeEmailWithTemplate();
        $employeeSubject = $template->getEmployeeSubject();
        if ($employeeSubject !== '') {
            foreach ($employeeEmail as $mail => $employeeTemplate) {
                $this->emailSender->sendMessage($employeeEmail, null, $employeeSubject, $data, $employeeTemplate);
            }
        }
        $subject = $template->getSubject();
        $emails = $template->getEmailsWithTemplates();
        foreach ($emails as $email => $template) {
            $this->emailSender->sendMessage($email, null, $subject, $data, $template);
        }
    }
}
