<?php

namespace Divante\Bundle\AdventureBundle\Services\Slack;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\EmployeeProject;
use Divante\Bundle\AdventureBundle\Entity\Project;
use Divante\Bundle\AdventureBundle\Filters\EmployeeProject\EmployeeProjectActive;
use Divante\Bundle\AdventureBundle\Services\Slack\Api\SlackReceiver;
use Divante\Bundle\AdventureBundle\Services\Slack\MessageTemplates\NewRequestStatusMessage;
use Doctrine\ORM\EntityManagerInterface;

class UpdateStatusNotification
{
    /** @var EntityManagerInterface */
    private $em;
    /** @var EmployeeProjectActive */
    private $filter;
    /** @var NewRequestStatusMessage */
    private $message;

    public function __construct(
        EntityManagerInterface $em,
        EmployeeProjectActive $filter,
        NewRequestStatusMessage $message
    ) {
        $this->em = $em;
        $this->filter = $filter;
        $this->message = $message;
    }

    public function notify(Employee $employee, ?int $type) : void
    {
        $this->notifyProjects($employee, $type);
        $tribe = $employee->getTribe();
        if (!is_null($tribe)) {
            $this->notifyReceiver($tribe, $employee, $type);
        }
    }

    private function notifyProjects(Employee $employee, ?int $type) : void
    {
        $repo = $this->em->getRepository(EmployeeProject::class);
        /** @var EmployeeProject[] $employeeProjects */
        $employeeProjects = $repo->findBy([
            'employee' => $employee
        ]);
        $activePairings = array_filter($employeeProjects, $this->filter);
        /** @var Project[] $projects */
        $projects = array_map([ $this, 'toProject' ], $activePairings);
        foreach ($projects as $project) {
            $this->notifyReceiver($project, $employee, $type);
        }
    }

    private function notifyReceiver(SlackReceiver $receiver, Employee $employee, ?int $type) : void
    {
        if (!$this->receiverGotMessageToday($receiver)) {
            return;
        }
        $this->message->setEmployee($employee)->send($receiver, $type);
    }

    private function receiverGotMessageToday(SlackReceiver $receiver) : bool
    {
        $lastTime = $receiver->getLastSlackMessageSent();
        if (is_null($lastTime)) {
            return false;
        }
        $date = $lastTime->format('Y-m-d');
        $currentDate = date('Y-m-d');
        return $date === $currentDate;
    }

    private function toProject(EmployeeProject $pairing) : Project
    {
        return $pairing->getProject();
    }
}
