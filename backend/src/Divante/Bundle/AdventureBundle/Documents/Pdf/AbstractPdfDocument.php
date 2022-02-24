<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 04.02.19
 * Time: 14:06
 */

namespace Divante\Bundle\AdventureBundle\Documents\Pdf;

use Divante\Bundle\AdventureBundle\Infrastructure\Config\EmailConfig;
use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;
use Swift_Attachment;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Environment;

abstract class AbstractPdfDocument
{
    public const DEFAULT_FONT = 'Arial';

    private Environment $twig;
    private string $mailerFromAddress;
    private Swift_Mailer $mailer;
    private KernelInterface $kernel;

    public function __construct(
        Environment $twig,
        EmailConfig $emailConfig,
        Swift_Mailer $mailer,
        KernelInterface $kernel
    ) {
        $this->twig = $twig;
        $this->mailerFromAddress = $emailConfig->getFromEmail();
        $this->mailer = $mailer;
        $this->kernel = $kernel;
    }

    /**
     * @param string|string[] $addresses
     * @throws Exception
     */
    final public function buildAndSendPdf($addresses) : void
    {
        $pdfPath = $this->buildPdf();
        $body = $this->buildEmailHtml($this->twig);
        $message = new Swift_Message();
        $message->setChildren([]);
        $message
            ->setSubject($this->getEmailSubject())
            ->setFrom($this->mailerFromAddress)
            ->setTo($addresses)
            ->setBody($body, 'text/html')
            ->attach(Swift_Attachment::fromPath($pdfPath));
        foreach ($this->getAdditionalAttachments() as $attachment) {
            $message->attach($attachment);
        }
        if ($this->mailer->send($message) === 0) {
            throw new Exception("Failed to sent message");
        }
    }

    /**
     * @return string path to generated PDF file
     * @throws Exception
     */
    final public function buildPdf() : string
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', self::DEFAULT_FONT)->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($pdfOptions);
        $html = $this->buildPdfHtml($this->twig);
        $dompdf->loadHtml(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $output = $dompdf->output();
        $pdfFilepath = $this->constructFullFilePath();
        file_put_contents($pdfFilepath, $output);
        return $pdfFilepath;
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

    private function constructFullFilePath() : string
    {
        $filesystem = new Filesystem();
        $pathInVar = $this->getFilePathInVar();
        $directory = '../var/'.dirname($pathInVar);
        $fullPath = "../var/$pathInVar";
        if (!$filesystem->exists($directory)) {
            $filesystem->mkdir($directory);
        }
        if ($filesystem->exists($fullPath)) {
            $filesystem->remove($fullPath);
        }
        $filesystem->touch($fullPath);

        $rootDir = $this->kernel->getRootDir();
        return "$rootDir/$fullPath";
    }

    /**
     * @return \Swift_Mime_Attachment[]
     */
    protected function getAdditionalAttachments() : array
    {
        return [];
    }

    abstract protected function buildPdfHtml(Environment $twig) : string;
    abstract protected function buildEmailHtml(Environment $twig) : string;
    abstract protected function getFilePathInVar() : string;
    abstract public function getEmailSubject() : string;
}
