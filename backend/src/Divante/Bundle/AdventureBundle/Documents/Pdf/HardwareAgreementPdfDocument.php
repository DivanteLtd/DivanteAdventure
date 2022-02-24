<?php

namespace Divante\Bundle\AdventureBundle\Documents\Pdf;

use Divante\Bundle\AdventureBundle\Entity\Hardware\HardwareAgreement;
use Divante\Bundle\AdventureBundle\Services\Hardware\Cipher;
use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

class HardwareAgreementPdfDocument
{
    private Cipher $cipher;
    private TranslatorInterface $translator;
    private Environment $twig;
    private KernelInterface $kernel;
    
    public function __construct(Cipher $cipher, TranslatorInterface $trans, Environment $twig, KernelInterface $kernel)
    {
        $this->cipher = $cipher;
        $this->translator = $trans;
        $this->twig = $twig;
        $this->kernel = $kernel;
    }

    public function buildPdf(HardwareAgreement $agreement, string $password, string $language) : string
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', AbstractPdfDocument::DEFAULT_FONT)->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($pdfOptions);
        $html = $this->buildHtml($agreement, $password, $language);
        $dompdf->loadHtml(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        return $dompdf->output();
    }
    
    private function buildHtml(HardwareAgreement $agreement, string $password, string $language) : string
    {
        $employee = $agreement->getAssignment()->getEmployee();
        $this->translator->setLocale($language);
        $signatureDate = $agreement->getSignedByHelpdesk()
            ? (new \DateTime())->format('Y-m-d') : null;
        $options = [
            'agreement' => $agreement,
            'pesel' => $this->decipher($agreement->getPesel(), $password, $agreement->getId()),
            'company' => $this->decipher($agreement->getCompany(), $password, $agreement->getId()),
            'name' => sprintf("%s %s", $agreement->getName(), $agreement->getLastName()),
            'headquarters' => $this->decipher($agreement->getHeadquarters(), $password, $agreement->getId()),
            'nip' => $this->decipher($agreement->getNip(), $password, $agreement->getId()),
            'document' => $this,
            'signatureDate' => $signatureDate
        ];
        $templateName = $this->getTemplateName($employee->getContractType());
        return $this->twig->render($templateName, $options);
    }

    private function decipher(?string $data, string $password, int $id) : ?string
    {
        return $this->cipher->decrypt($data, $password, $id);
    }

    private function getTemplateName(string $contract) : string
    {
        switch ($contract) {
            case "B2B HOURLY":
            case "B2B LUMP SUM":
                return "AdventureBundle:pdf:hardware/B2B.html.twig";
            case "CLC HOURLY":
            case "CLC LUMP SUM":
                return "AdventureBundle:pdf:hardware/CLC.html.twig";
            case "CoE":
                return "AdventureBundle:pdf:hardware/CoE.html.twig";
            default:
                throw new Exception("Unrecognized contract name: $contract");
        }
    }

    public function getLogo() : string
    {
        $finder = new Finder();
        $finder->files()
            ->in($this->kernel->getRootDir().'/../web')
            ->ignoreUnreadableDirs()
            ->name('logo-white.png');
        foreach ($finder as $file) {
            $type = $file->getExtension();
            $data = $file->getContents();
            return 'data:image/'.$type.';base64,'.base64_encode($data);
        }
        return '';
    }
}
