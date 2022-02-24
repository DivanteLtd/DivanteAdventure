<?php

namespace Divante\Bundle\AdventureBundle\Command\Integration\Slack;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\EmployeeProject;
use Divante\Bundle\AdventureBundle\Entity\Project;
use Divante\Bundle\AdventureBundle\Entity\Tribe;
use Divante\Bundle\AdventureBundle\Filters\Employee\EmployeeLeftToday;
use Divante\Bundle\AdventureBundle\Filters\Employee\EmployeeOnDelegationToday;
use Divante\Bundle\AdventureBundle\Filters\Employee\EmployeeRemoteToday;
use Divante\Bundle\AdventureBundle\Filters\EmployeeProject\EmployeeProjectActive;
use Divante\Bundle\AdventureBundle\Services\Slack\Api\SlackReceiver;
use Divante\Bundle\AdventureBundle\Services\Slack\Api\SlackSender;
use Divante\Bundle\AdventureBundle\Services\Slack\MessageTemplates\LeaveStatusMessage;
use Divante\Bundle\AdventureBundle\Supplier\FreeDaysSupplier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LeaveNotificationCommand extends ContainerAwareCommand
{
    private EntityManagerInterface $em;
    private SlackSender $sender;
    private LeaveStatusMessage $messageTemplate;
    private EmployeeLeftToday $leftTodayFilter;
    private EmployeeOnDelegationToday $delegationFilter;
    private EmployeeRemoteToday $remoteFilter;
    private EmployeeProjectActive $pairingActiveFilter;
    private FreeDaysSupplier $freeDaysSupplier;

    public function __construct(
        EntityManagerInterface $em,
        SlackSender $sender,
        LeaveStatusMessage $template,
        EmployeeLeftToday $leftTodayFilter,
        EmployeeOnDelegationToday $delegationFilter,
        EmployeeRemoteToday $remoteFilter,
        EmployeeProjectActive $pairingFilter,
        FreeDaysSupplier $freeDaysSupplier
    ) {
        parent::__construct(null);
        $this->em = $em;
        $this->sender = $sender;
        $this->messageTemplate = $template;
        $this->leftTodayFilter = $leftTodayFilter;
        $this->delegationFilter = $delegationFilter;
        $this->remoteFilter = $remoteFilter;
        $this->pairingActiveFilter = $pairingFilter;
        $this->freeDaysSupplier = $freeDaysSupplier;
    }

    protected function configure() : void
    {
        $this
            ->setName('adventure:slack:leave-notify')
            ->setDescription(
                'Notify project and tribe channels about leaves today'
            );
    }

    public function run(InputInterface $input, OutputInterface $output) : int
    {
        $currentYear = (int)date('Y');
        $freeDays = $this->freeDaysSupplier->getFreeDays($currentYear, $currentYear);
        if (in_array(date('Y-m-d'), $freeDays)) {
            $output->writeln("Today is a free day; don't send messages");
            return 0;
        }
        $this->sendTribesNotifications($output);
        $this->sendProjectsNotifications($output);
        $this->em->flush();
        return 0;
    }

    private function sendTribesNotifications(OutputInterface $output) : void
    {
        $tribeRepo = $this->em->getRepository(Tribe::class);
        /** @var Tribe[] $tribes */
        $tribes = $tribeRepo->findBy([
            'slackStatus' => SlackReceiver::SLACK_AUTHORIZED
        ]);
        foreach ($tribes as $tribe) {
            $this->sendTribeNotification($output, $tribe);
        }
    }

    private function sendTribeNotification(OutputInterface $output, Tribe $tribe) : void
    {
        /** @var Employee[] $employees */
        $employees = $tribe->getEmployees()->toArray();
        if (count($employees) > 0) {
            $this->notify($output, $tribe, $employees);
        }
    }

    private function sendProjectsNotifications(OutputInterface $output) : void
    {
        $projectRepo = $this->em->getRepository(Project::class);

        /** @var Project[] $projects */
        $projects = $projectRepo->findBy([
            'slackStatus' => SlackReceiver::SLACK_AUTHORIZED
        ]);

        foreach ($projects as $project) {
            $this->sendProjectNotification($output, $project);
        }
    }

    private function sendProjectNotification(OutputInterface $output, Project $project) : void
    {
        $employeeProjectRepo = $this->em->getRepository(EmployeeProject::class);
        /** @var EmployeeProject[] $pairings */
        $pairings = $employeeProjectRepo->findBy([
            'project' => $project
        ]);

        $activePairings = array_filter($pairings, $this->pairingActiveFilter);
        /** @var Employee[] $employees */
        $employees = array_map(
            function (EmployeeProject $pairing) {
                return $pairing->getEmployee();
            },
            $activePairings
        );
        if (count($employees) > 0) {
            $this->notify($output, $project, $employees);
        }
    }

    /**
     * @param OutputInterface $output
     * @param SlackReceiver $receiver
     * @param Employee[] $employees
     */
    private function notify(OutputInterface $output, SlackReceiver $receiver, array $employees) : void
    {
        $output->write("Sending statuses to {$receiver->getName()}... ");
        $leavingEmployees = array_filter($employees, $this->leftTodayFilter);
        $delegatedEmployees = array_filter($employees, $this->delegationFilter);
        $remoteEmployees = array_filter($employees, $this->remoteFilter);
        $message = $this->messageTemplate
            ->setReceiver($receiver)
            ->setLeavingEmployees($leavingEmployees)
            ->setDelegatedEmployees($delegatedEmployees)
            ->setRemoteEmployees($remoteEmployees)
            ->getMessage();
        $this->sender->send($message, $receiver);
        $output->writeln("done.");
    }
}
