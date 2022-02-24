<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 22.03.19
 * Time: 10:56
 */

namespace Divante\Bundle\AdventureBundle\Services\RequestMailer\Templates;

use Divante\Bundle\AdventureBundle\Entity\Employee;

class ResignationAcceptedTemplate extends AbstractTemplate
{
    public function getSubject(): string
    {
        $employee = $this->getRequest()->getEmployee();
        $this->translator->setLocale('pl');
        return sprintf(
            '[Adventure]%s #%d %s %s - %s',
            $this->getSpecialTypeSign(),
            $this->getRequest()->getId(),
            $employee->getName(),
            $employee->getLastName(),
            $this->translator->trans('requestForResignation')
        );
    }

    public function getEmailsWithTemplates(): array
    {
        $employee = $this->getRequest()->getEmployee();
        $this->translator->setLocale('pl');
        $recipients = [
            $this->getAdventureDivEmail() => 'AdventureBundle:Emails:leave_request_resignation_accepted.html.twig'
        ];
        $accountant = [];
        if ($employee->getContractId() === Employee::CONTRACT_COE) {
            $accountant = [
                $this->getAccountantEmail() =>
                    'AdventureBundle:Emails:leave_request_resignation_accepted.html.twig'
            ];
        }
        $recipients = array_merge(
            $recipients,
            $accountant,
            array_fill_keys(
                $this->getAdminEmails(),
                'AdventureBundle:Emails:leave_request_resignation_accepted.html.twig'
            )
        );
        return $recipients;
    }

    public function getEmployeeEmailWithTemplate(): array
    {
        $employee = $this->getRequest()->getEmployee();
        $language = $employee->getLanguage();
        $this->translator->setLocale($language);
        return [$employee->getEmail() => 'AdventureBundle:Emails:leave_request_resignation_accepted.html.twig'];
    }

    public function getEmployeeSubject(): string
    {
        $employee = $this->getRequest()->getEmployee();
        $language = $employee->getLanguage();
        $this->translator->setLocale($language);
        $subject = $this->translator->trans('resignationFromRequest');
        return sprintf(
            '[#%d]%s %s %s - %s',
            $this->getRequest()->getId(),
            $this->getSpecialTypeSign(),
            $employee->getName(),
            $employee->getLastName(),
            $subject
        );
    }
}
