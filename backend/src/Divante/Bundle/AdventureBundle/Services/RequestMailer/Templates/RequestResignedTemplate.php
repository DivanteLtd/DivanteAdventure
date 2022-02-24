<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 22.03.19
 * Time: 10:55
 */

namespace Divante\Bundle\AdventureBundle\Services\RequestMailer\Templates;

class RequestResignedTemplate extends AbstractTemplate
{

    public function getSubject(): string
    {
        $employee = $this->getRequest()->getEmployee();
        $language = $this->getRequest()->getManager()->getLanguage();
        $this->translator->setLocale($language);
        $subject = $this->translator->trans('requestWasAbandoned');
        return sprintf(
            '[#%d] %s %s %s - ' . $subject,
            $this->getRequest()->getId(),
            $this->getSpecialTypeSign(),
            $employee->getName(),
            $employee->getLastName()
        );
    }

    public function getEmailsWithTemplates(): array
    {
        $manager = $this->getRequest()->getManager();
        $language = $manager->getLanguage();
        $this->translator->setLocale($language);
        return [
            $manager->getEmail() => 'AdventureBundle:Emails:leave_request_deleted.html.twig',
        ];
    }

    public function getEmployeeEmailWithTemplate(): array
    {
        return [];
    }

    public function getEmployeeSubject(): string
    {
        return '';
    }
}
