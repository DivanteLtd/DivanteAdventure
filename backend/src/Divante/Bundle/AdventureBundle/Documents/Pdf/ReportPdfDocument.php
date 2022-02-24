<?php

namespace Divante\Bundle\AdventureBundle\Documents\Pdf;

use Divante\Bundle\AdventureBundle\Entity\Project;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Environment;

class ReportPdfDocument extends AbstractPdfDocument
{
    /** @var mixed[]|null */
    private ?array $history = null;
    private Project $project;
    /** @var mixed[]|null */
    private ?array $employeeProject = null;
    private string $fileName;

    protected function buildPdfHtml(Environment $twig): string
    {
        return $twig->render('AdventureBundle:pdf:report.html.twig', ['document' => $this]);
    }

    protected function buildEmailHtml(Environment $twig): string
    {
        return '';
    }

    protected function getFilePathInVar(): string
    {
        $publicDirectory = 'tmp/reports';
        $fileName = sprintf('%s.pdf', $this->fileName);
        $pdfPath = "$publicDirectory/$fileName";
        return $pdfPath;
    }

    public function getEmailSubject(): string
    {
        return '';
    }

    /** @return mixed[]|null */
    public function getHistory(): ?array
    {
        return $this->history;
    }

    /** @param mixed[]|null $history */
    public function setHistory(?array $history): void
    {
        $this->history = $history;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function setProject(Project $project): void
    {
        $this->project = $project;
    }

    /** @return mixed[]|null */
    public function getEmployeeProject(): ?array
    {
        return $this->employeeProject;
    }

    /** @param mixed[]|null $employeeProject */
    public function setEmployeeProject(?array $employeeProject): void
    {
        $this->employeeProject = $employeeProject;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): void
    {
        $this->fileName = $fileName;
    }
}
