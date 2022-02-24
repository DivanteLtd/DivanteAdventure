<?php

namespace Divante\Bundle\AdventureBundle\Command\Integration\Avaza;

use Divante\Bundle\AdventureBundle\Command\ResponseConverterTrait;
use Divante\Bundle\AdventureBundle\Entity\LeaveRequestDay;
use Divante\Bundle\AdventureBundle\Entity\Repository\LeaveRequestDayRepository;
use Divante\Bundle\AdventureBundle\Infrastructure\Config\SystemConfig;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Response;

class AvazaLeaveCommand extends Command
{
    /** @var string */
    private const ENTRYPOINT_URL = 'https://api.avaza.com/api/Timesheet';

    private EntityManagerInterface $em;
    private string $token;
    private string $sickLeaveProjectId;
    private string $sickLeaveCategoryId;
    private string $freeDayProjectId;
    private string $freeDayCategoryId;

    use ResponseConverterTrait;

    public function __construct(SystemConfig $config, EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
        $this->token = $config->getValueOrDefault(SystemConfig::KEY_AVAZA_TOKEN, '');
        $this->sickLeaveProjectId = $config->getValueOrDefault(SystemConfig::KEY_AVAZA_SICK_LEAVE_PROJECT_ID, '');
        $this->sickLeaveCategoryId = $config->getValueOrDefault(SystemConfig::KEY_AVAZA_SICK_LEAVE_CATEGORY_ID, '');
        $this->freeDayProjectId = $config->getValueOrDefault(SystemConfig::KEY_AVAZA_FREE_DAY_PROJECT_ID, '');
        $this->freeDayCategoryId = $config->getValueOrDefault(SystemConfig::KEY_AVAZA_FREE_DAY_CATEGORY_ID, '');
    }

    protected function configure(): void
    {
        $this->setName('adventure:avaza:leaves')
            ->setDescription("Synchronizes leave and sick days with Avaza");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (empty($this->token)
            || empty($this->sickLeaveProjectId)
            || empty($this->freeDayProjectId)
            || empty($this->freeDayCategoryId)
            || empty($this->sickLeaveCategoryId)
        ) {
            $output->writeln("Configuration is not supplied; stopping");
            return 1;
        }
        /** @var LeaveRequestDayRepository $repo */
        $repo = $this->em->getRepository(LeaveRequestDay::class);

        $toSend = $repo->getDaysToSendToAvaza();
        $toRemove = $repo->getDaysToRemoveFromAvaza();
        $client = new Client();
        foreach ($toSend as $entry) {
            try {
                $this->sendToAvaza($entry, $client, $output);
            } catch (\Exception $e) {
                $output->writeln($e->getMessage());
                $entry->setAvazaSyncStatus(LeaveRequestDay::AVAZA_STATUS_FAILED);
            }
        }
        foreach ($toRemove as $entry) {
            try {
                $this->removeFromAvaza($entry, $client, $output);
            } catch (\Exception $e) {
                $output->writeln($e->getMessage());
                $entry->setAvazaSyncStatus(LeaveRequestDay::AVAZA_STATUS_FAILED);
            }
        }

        $output->writeln("Flushing...");
        $this->em->flush();
        return 0;
    }

    private function sendToAvaza(LeaveRequestDay $entry, Client $client, OutputInterface $output): void
    {
        $employee = $entry->getEmployee();
        if (is_null($employee) || is_null($employee->getAvazaId()) || $employee->getAvazaId() <= 0) {
            return;
        }
        if ($this->isNotPaid($entry)) {
            return;
        }
        $output->writeln(sprintf(
            "Creating entry for %s %s on date %s...",
            $employee->getName(),
            $employee->getLastName(),
            $entry->getDate()->format('Y-m-d'),
        ));
        $sickLeaveProjectId = $this->sickLeaveProjectId;
        $freeDayProjectId = $this->freeDayProjectId;
        $sickLeaveCategoryId = $this->sickLeaveCategoryId;
        $freeDayCategoryId = $this->freeDayCategoryId;

        $tribe = $employee->getTribe();
        if (!is_null($tribe)) {
            if (!empty($tribe->getSickLeaveProjectId())) {
                $sickLeaveProjectId = $tribe->getSickLeaveProjectId();
            }
            if (!empty($tribe->getSickLeaveCategoryId())) {
                $sickLeaveCategoryId = $tribe->getSickLeaveCategoryId();
            }
            if (!empty($tribe->getFreeDayProjectId())) {
                $freeDayProjectId = $tribe->getFreeDayProjectId();
            }
            if (!empty($tribe->getFreeDayCategoryId())) {
                $freeDayCategoryId = $tribe->getFreeDayCategoryId();
            }
        }

        $projectId = $this->isSickLeave($entry) ? $sickLeaveProjectId : $freeDayProjectId;
        $categoryId = $this->isSickLeave($entry) ? $sickLeaveCategoryId : $freeDayCategoryId;
        $response = $client->post(self::ENTRYPOINT_URL, [
            RequestOptions::JSON => [
                "UserIDFK" => $employee->getAvazaId(),
                "ProjectIDFK" => $projectId,
                "TimesheetCategoryIDFK" => $categoryId,
                "Duration" => 8,
                "isInvoiced" => false,
                "EntryDate" => $entry->getDate()->format("Y-m-d"),
                "hasStartEndTime" => false,
            ],
            RequestOptions::HEADERS => [
                'Accept' => 'application/json',
                'Authorization' => "Bearer $this->token",
            ],
        ]);
        $responseString = $this->streamInterfaceToString($response->getBody());
        if ($response->getStatusCode() == Response::HTTP_OK) {
            $json = json_decode($responseString, true);
            $avazaId = $json['TimesheetEntryID'] ?? null;
            $entry
                ->setAvazaId($avazaId)
                ->setAvazaSyncStatus(LeaveRequestDay::AVAZA_STATUS_SYNCED);
        } else {
            $output->writeln("Response: $responseString");
        }
    }

    private function removeFromAvaza(LeaveRequestDay $entry, Client $client, OutputInterface $output): void
    {
        $employee = $entry->getEmployee();
        if (is_null($employee) || is_null($entry->getAvazaId())
            || $entry->getAvazaId() <= 0 || $employee->getAvazaId() <= 0) {
            return;
        }
        $output->writeln(sprintf(
            "Deleting entry for %s %s on date %s...",
            $employee->getName(),
            $employee->getLastName(),
            $entry->getDate()->format('Y-m-d'),
        ));
        $url = sprintf("%s/%s", self::ENTRYPOINT_URL, $entry->getAvazaId());
        $response = $client->delete($url, [
            RequestOptions::HEADERS => [
                'Accept' => 'application/json',
                'Authorization' => "Bearer $this->token",
            ],
        ]);
        $responseString = $this->streamInterfaceToString($response->getBody());
        if ($response->getStatusCode() == Response::HTTP_OK) {
            $entry
                ->setAvazaId(null)
                ->setAvazaSyncStatus(LeaveRequestDay::AVAZA_STATUS_NOT_SYNCED);
        } else {
            $output->writeln("Response: $responseString");
        }
    }

    private function isSickLeave(LeaveRequestDay $entry): bool
    {
        return $entry->getType() === LeaveRequestDay::DAY_TYPE_SICK_LEAVE_PAID;
    }
    
    private function isNotPaid(LeaveRequestDay $entry): bool
    {
        return in_array(
            $entry->getType(),
            [
                LeaveRequestDay::DAY_TYPE_LEAVE_UNPAID,
                LeaveRequestDay::DAY_TYPE_FREE_UNPAID,
                LeaveRequestDay::DAY_TYPE_OVERTIME,
                LeaveRequestDay::DAY_TYPE_ADDITIONAL_HOURS,
                LeaveRequestDay::DAY_TYPE_UNAVAILABILITY,
            ],
        );
    }
}
