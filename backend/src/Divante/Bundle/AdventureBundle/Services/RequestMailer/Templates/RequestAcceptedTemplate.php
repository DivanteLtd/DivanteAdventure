<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 22.03.19
 * Time: 10:54
 */

namespace Divante\Bundle\AdventureBundle\Services\RequestMailer\Templates;

use Divante\Bundle\AdventureBundle\Entity\Employee;

class RequestAcceptedTemplate extends AbstractTemplate
{

    public function getSubject(): string
    {
        $requestDaysNumber = count($this->getRequest()->getRequestDays());
        $this->translator->setLocale('pl');
        return sprintf(
            '%s [#%d]%s  %s %s - %s',
            $requestDaysNumber < 20 ? '' : $this->translator->trans('Attention'),
            $this->getRequest()->getId(),
            $this->getSpecialTypeSign(),
            $this->getRequest()->getEmployee()->getName(),
            $this->getRequest()->getEmployee()->getLastName(),
            $this->translator->trans('requestHasBeenApproved')
        );
    }

    public function getEmailsWithTemplates(): array
    {
        $this->translator->setLocale('pl');
        $employee = $this->getRequest()->getEmployee();
        $recipients = [
            $this->getAdventureDivEmail() => 'AdventureBundle:Emails:leave_request_accepted.html.twig'
        ];
        $accountant = [];
        if ($employee->getContractId() === Employee::CONTRACT_COE) {
            $accountant = [
                $this->getAccountantEmail() => 'AdventureBundle:Emails:leave_request_accepted.html.twig'
            ];
        }
        $recipients = array_merge(
            $recipients,
            $accountant,
            array_fill_keys(
                $this->getAdminEmails(),
                'AdventureBundle:Emails:leave_request_accepted.html.twig'
            )
        );
        return $recipients;
    }

    public function getEmployeeEmailWithTemplate(): array
    {
        $employee = $this->getRequest()->getEmployee();
        $language = $employee->getLanguage();
        $this->translator->setLocale($language);
        return [ $employee->getEmail() => 'AdventureBundle:Emails:leave_request_accepted.html.twig' ];
    }

    public function getEmployeeSubject(): string
    {
        $employee = $this->getRequest()->getEmployee();
        $language = $employee->getLanguage();
        $this->translator->setLocale($language);
        $subject = $this->translator->trans('requestHasBeenApproved');
        return sprintf(
            '[#%d]%s %s %s - %s',
            $this->getRequest()->getId(),
            $this->getSpecialTypeSign(),
            $this->getRequest()->getEmployee()->getName(),
            $this->getRequest()->getEmployee()->getLastName(),
            $subject
        );
    }
}
