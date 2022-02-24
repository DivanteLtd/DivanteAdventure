<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler;

use Divante\Bundle\AdventureBundle\Message\CreatePlannerReport;
use Divante\Bundle\AdventureBundle\Representation\Planner\ReportConfiguration;

class CreatePlannerReportHandler
{
    private ReportConfiguration $reportConfiguration;

    public function __construct(ReportConfiguration $reportConfiguration)
    {
        $this->reportConfiguration = $reportConfiguration;
    }

    public function __invoke(CreatePlannerReport $message) : void
    {
        $data = $message->getData();
        $service = $this->reportConfiguration;
        $service->setStructure($data);
        $service->generateReport($message->getFilePath());
    }
}
