<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 22.03.19
 * Time: 11:02
 */

namespace Divante\Bundle\AdventureBundle\Services\RequestMailer\Templates;

class NullTemplate extends AbstractTemplate
{

    public function getSubject(): string
    {
        return '';
    }

    public function getEmployeeSubject(): string
    {
        return '';
    }

    public function getEmailsWithTemplates(): array
    {
        return [];
    }

    public function getEmployeeEmailWithTemplate(): array
    {
        return [];
    }
}
