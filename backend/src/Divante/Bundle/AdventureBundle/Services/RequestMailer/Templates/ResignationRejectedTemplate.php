<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 22.03.19
 * Time: 10:58
 */

namespace Divante\Bundle\AdventureBundle\Services\RequestMailer\Templates;

class ResignationRejectedTemplate extends AbstractTemplate
{

    public function getSubject(): string
    {
        $employee = $this->getRequest()->getEmployee();
        $language = $employee->getLanguage();
        $this->translator->setLocale($language);
        $subject = $this->translator->trans('yourResignationFromRequestHasBeenRejected');
        return sprintf(
            '[#%d]%s %s %s - %s',
            $this->getRequest()->getId(),
            $this->getSpecialTypeSign(),
            $employee->getName(),
            $employee->getLastName(),
            $subject
        );
    }

    public function getEmailsWithTemplates(): array
    {
        $employee = $this->getRequest()->getEmployee();
        $language = $employee->getLanguage();
        $this->translator->setLocale($language);
        return [
            $employee->getEmail() => 'AdventureBundle:Emails:leave_request_resignation_rejected.html.twig'
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
