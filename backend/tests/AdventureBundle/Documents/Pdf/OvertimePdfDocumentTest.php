<?php

namespace Tests\AdventureBundle\Documents\Pdf;

use Divante\Bundle\AdventureBundle\Documents\Pdf\AbstractEvidencePdfDocument;
use Divante\Bundle\AdventureBundle\Documents\Pdf\OvertimePdfDocument;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Evidence;
use Divante\Bundle\AdventureBundle\Infrastructure\Config\EmailConfig;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

class OvertimePdfDocumentTest extends TestCase
{
    public function testEvidenceEmailSubject() : void
    {
        $randomName = 'RandomName'.rand(0, 10000);
        $randomLastName = 'RandomLastName'.rand(0, 10000);
        $randomYear = rand(1971, 2050);
        $randomMonth = rand(1, 12);

        $employee = new Employee();
        $employee
            ->setName($randomName)
            ->setLastName($randomLastName)
            ->setContractId(rand(1, 3));

        $evidence = new Evidence();
        $evidence
            ->setMonth($randomMonth)
            ->setYear($randomYear)
            ->setEmployee($employee);

        $document = new OvertimePdfDocument(
            $this->createMock(Environment::class),
            $this->createMock(EmailConfig::class),
            $this->createMock(\Swift_Mailer::class),
            $this->createMock(Kernel::class),
            $this->createMock(TranslatorInterface::class),
            $this->createMock(EntityManagerInterface::class),
        );
        $document->setEvidence($evidence);
        $subject = $document->getEmailSubject();

        $this->assertStringContainsString($randomName, $subject);
        $this->assertStringContainsString($randomLastName, $subject);
        $this->assertStringContainsString($randomYear, $subject);
        $this->assertStringContainsString(AbstractEvidencePdfDocument::getMonth($randomMonth), $subject);
    }
}
