<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 21.01.19
 * Time: 14:54
 */

namespace Divante\Bundle\AdventureBundle\Documents\Pdf;

use Divante\Bundle\AdventureBundle\Controller\Api\EvidenceController;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\EvidenceInvoice;

class EvidencePdfDocument extends AbstractEvidencePdfDocument
{
    /**
     * @return string
     * @throws \Exception
     */
    public function getEmailSubject() : string
    {
        $subject = $this->translator->trans('recordsOfHours');
        $contractName = Employee::getContractNameById($this->evidence->getEmployee()->getContractId());
        return sprintf(
            "[%s] %s - %s %s (%s %d)",
            $this->translator->trans($contractName),
            $subject,
            $this->evidence->getEmployee()->getName(),
            $this->evidence->getEmployee()->getLastName(),
            self::getMonth($this->evidence->getMonth()),
            $this->evidence->getYear()
        );
    }

    protected function getFilePathInVar() : string
    {
        $language = $this->translator->getLocale();
        $year = $this->evidence->getYear();
        $month = self::getMonth($this->evidence->getMonth());
        $publicDirectory = "evidences/Y$year/$month";
        $fileName = $language . "_" . $this->evidence->getEmployee()->getLastName() .
            "_" . $this->evidence->getEmployee()->getName() . ".pdf";
        $pdfPath = "$publicDirectory/$fileName";
        return $pdfPath;
    }

    protected function getAdditionalAttachments(): array
    {
        $return = [];
        /** @var EvidenceInvoice $invoice */
        foreach ($this->evidence->getInvoices() as $invoice) {
            $storeFileName = $invoice->getPath();
            $fullPath = '../web/uploads/'.EvidenceController::EVIDENCE_INVOICE_DIRECTORY.'/'.$storeFileName;
            if (!file_exists($fullPath)) {
                continue;
            }
            $stream = new \Swift_ByteStream_FileByteStream($fullPath);
            $attachmentName = $invoice->getName();
            $return[] = new \Swift_Attachment($stream, $attachmentName);
        }
        return $return;
    }

    protected function getPdfTemplate(): string
    {
        return "AdventureBundle:pdf:evidence.html.twig";
    }

    protected function getEmailTemplate(): string
    {
        return "AdventureBundle:Emails:evidence_mail.html.twig";
    }
}
