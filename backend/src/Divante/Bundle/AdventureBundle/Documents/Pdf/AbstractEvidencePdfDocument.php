<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 05.02.19
 * Time: 08:11
 */

namespace Divante\Bundle\AdventureBundle\Documents\Pdf;

use Divante\Bundle\AdventureBundle\Entity\Company;
use Divante\Bundle\AdventureBundle\Entity\Evidence;
use Divante\Bundle\AdventureBundle\Infrastructure\Config\EmailConfig;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

abstract class AbstractEvidencePdfDocument extends AbstractPdfDocument
{
    protected ?Evidence $evidence = null;
    protected TranslatorInterface $translator;
    private EntityManagerInterface $entityManager;

    public function __construct(
        Environment $twig,
        EmailConfig $emailConfig,
        \Swift_Mailer $mailer,
        KernelInterface $kernel,
        TranslatorInterface $translator,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($twig, $emailConfig, $mailer, $kernel);
        $this->translator = $translator;
        $this->entityManager = $entityManager;
    }

    public static function getMonth(?int $monthNum): string
    {
        return date('m', mktime(0, 0, 0, $monthNum));
    }

    public function getEvidence(): ?Evidence
    {
        return $this->evidence;
    }

    public function setEvidence(Evidence $evidence): void
    {
        $this->evidence = $evidence;
    }

    /**
     * @param Environment $twig
     * @return string
     * @throws \Exception
     */
    final protected function buildPdfHtml(Environment $twig): string
    {
        $companyRepo = $this->entityManager->getRepository(Company::class)->find(1);
        $company = [
            'name' => $companyRepo->getName(),
            'address' => $companyRepo->getAddress(),
            'vatId' => $companyRepo->getVatId()
        ];
        return $twig->render($this->getPdfTemplate(), ['document' => $this, 'company' => $company]);
    }

    /**
     * @param Environment $twig
     * @return string
     * @throws \Exception
     */
    final protected function buildEmailHtml(Environment $twig): string
    {
        return $twig->render($this->getEmailTemplate(), ['document' => $this]);
    }

    abstract protected function getPdfTemplate(): string;
    abstract protected function getEmailTemplate(): string;
}
