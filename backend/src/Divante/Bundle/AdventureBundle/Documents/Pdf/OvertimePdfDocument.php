<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 05.02.19
 * Time: 07:54
 */

namespace Divante\Bundle\AdventureBundle\Documents\Pdf;

use Divante\Bundle\AdventureBundle\Entity\Employee;

class OvertimePdfDocument extends AbstractEvidencePdfDocument
{
    protected function getFilePathInVar(): string
    {
        $language = $this->translator->getLocale();
        $year = $this->evidence->getYear();
        $month = self::getMonth($this->evidence->getMonth());
        $publicDirectory = "evidences/overtime/Y$year/$month";
        $fileName = $language . "_" . $this->evidence->getEmployee()->getLastName() .
            "_" . $this->evidence->getEmployee()->getName() . ".pdf";
        $pdfPath = "$publicDirectory/$fileName";
        return $pdfPath;
    }

    public function getEmailSubject(): string
    {
        $employee = $this->evidence->getEmployee();
        $subject = $this->translator->trans('recordsOfAdditionalHours');
        $contractName = Employee::getContractNameById($employee->getContractId());
        return sprintf(
            "[%s] %s - %s %s (%s %d)",
            $this->translator->trans($contractName),
            $subject,
            $employee->getName(),
            $employee->getLastName(),
            self::getMonth($this->evidence->getMonth()),
            $this->evidence->getYear()
        );
    }

    protected function getPdfTemplate(): string
    {
        return "AdventureBundle:pdf:evidence_overtime.html.twig";
    }

    protected function getEmailTemplate(): string
    {
        return "AdventureBundle:Emails:evidence_overtime_mail.html.twig";
    }
}
