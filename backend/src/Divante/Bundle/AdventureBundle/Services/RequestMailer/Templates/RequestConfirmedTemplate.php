<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\Services\RequestMailer\Templates;

class RequestConfirmedTemplate extends AbstractTemplate
{

    public function getSubject(): string
    {
        $this->translator->setLocale('en');
        return sprintf(
            '[Adventure]%s #%d %s %s - %s',
            $this->getSpecialTypeSign(),
            $this->getRequest()->getId(),
            $this->getRequest()->getEmployee()->getName(),
            $this->getRequest()->getEmployee()->getLastName(),
            $this->translator->trans('scheduledRequestHasBeenConfirmed')
        );
    }

    public function getEmailsWithTemplates(): array
    {
        $this->translator->setLocale('pl');
        return array_merge(
            [
                $this->getAdventureDivEmail() => 'AdventureBundle:Emails:leave_request_new.html.twig'
            ],
            array_fill_keys(
                $this->getAdminEmails(),
                'AdventureBundle:Emails:leave_request_new.html.twig'
            )
        );
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
