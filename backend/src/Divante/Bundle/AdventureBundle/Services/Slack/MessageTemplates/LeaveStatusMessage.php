<?php

namespace Divante\Bundle\AdventureBundle\Services\Slack\MessageTemplates;

use DateTime;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Project;
use Divante\Bundle\AdventureBundle\Entity\Tribe;
use Divante\Bundle\AdventureBundle\Filters\Employee\EmployeeLeftToday;
use Divante\Bundle\AdventureBundle\Services\Slack\Api\SlackReceiver;
use Divante\Bundle\AdventureBundle\Services\Slack\SlackMessage;
use Divante\Bundle\AdventureBundle\Services\Slack\SlackSendableMessage;
use Divante\Bundle\AdventureBundle\Supplier\FreeDaysSupplier;
use Symfony\Component\Translation\TranslatorInterface;

class LeaveStatusMessage
{
    private SlackReceiver $receiver;
    private TranslatorInterface $translator;
    /** @var Employee[] */
    private array $leavingEmployees = [];
    /** @var Employee[] */
    private array $delegatedEmployees = [];
    /** @var Employee[] */
    private array $remoteEmployees = [];
    private EmployeeLeftToday $employeeLeftToday;
    private FreeDaysSupplier $freeDaysSupplier;

    public function __construct(
        TranslatorInterface $translator,
        EmployeeLeftToday $employeeLeftToday,
        FreeDaysSupplier $freeDaysSupplier
    ) {
        $this->translator = $translator;
        $this->employeeLeftToday = $employeeLeftToday;
        $this->freeDaysSupplier = $freeDaysSupplier;
    }

    public function setReceiver(SlackReceiver $receiver) : self
    {
        $this->receiver = $receiver;
        return $this;
    }

    /**
     * @param Employee[] $employees
     * @return $this
     */
    public function setLeavingEmployees(array $employees) : self
    {
        $this->leavingEmployees = $employees;
        return $this;
    }

    /**
     * @param Employee[] $employees
     * @return $this
     */
    public function setDelegatedEmployees(array $employees) : self
    {
        $this->delegatedEmployees = $employees;
        return $this;
    }

    /**
     * @param Employee[] $employees
     * @return $this
     */
    public function setRemoteEmployees(array $employees): self
    {
        $this->remoteEmployees = $employees;
        return $this;
    }

    public function getMessage() : SlackSendableMessage
    {
        $this->translator->setLocale($this->receiver->getSlackLanguage());
        if (count($this->leavingEmployees) === 0) {
            $message = sprintf(
                $this->translator->trans('slack.status.everyonePresent'),
                $this->getName()
            );
        } else {
            $message = sprintf(
                $this->translator->trans('slack.status.notPresent'),
                $this->getName(),
                $this->prepareList($this->leavingEmployees, true)
            );
        }
        if (count($this->delegatedEmployees) > 0) {
            $message .= sprintf(
                $this->translator->trans('slack.status.delegations'),
                $this->prepareList($this->delegatedEmployees)
            );
        }
        if (count($this->remoteEmployees) > 0) {
            $message .= sprintf(
                $this->translator->trans('slack.status.remoteWork'),
                $this->prepareList($this->remoteEmployees)
            );
        }
        return new SlackMessage($message);
    }

    /**
     * @param Employee[] $employees
     * @param bool $addEndingDate
     * @return string
     */
    private function prepareList(array $employees, bool $addEndingDate = false) : string
    {
        usort(
            $employees,
            function (Employee $a, Employee $b) : int {
                $aName = sprintf("%s %s", $a->getLastName(), $a->getName());
                $bName = sprintf("%s %s", $b->getLastName(), $b->getName());
                return strcasecmp($aName, $bName);
            }
        );
        $names = array_map(fn(Employee $e) => $this->prepareEntry($e, $addEndingDate), $employees);
        return '- '.implode("\n- ", $names);
    }

    private function prepareEntry(Employee $employee, bool $addEndingDate): string
    {
        $name = $employee->getName().' '.$employee->getLastName();
        if (!$addEndingDate) {
            return $name;
        }
        $checker = $this->employeeLeftToday;
        $date = new DateTime();
        $dayInterval = \DateInterval::createFromDateString('1 day');
        while ($checker($employee, $date)
            || $this->freeDaysSupplier->isFreeDay($date)
            || $date->format('N') >= 6) {
            $date->add($dayInterval);
        }
        return sprintf("%s (available on %s)", $name, $date->format('d.m.Y'));
    }

    private function getName() : string
    {
        $name = "*".$this->receiver->getName()."*";
        if ($this->receiver instanceof Project) {
            return sprintf(
                $this->translator->trans('slack.status.projectTemplate'),
                $name,
            );
        } elseif ($this->receiver instanceof Tribe && $this->receiver->isVirtual()) {
            return sprintf(
                $this->translator->trans('slack.status.departmentTemplate'),
                $name,
            );
        } elseif ($this->receiver instanceof Tribe) {
            return sprintf(
                $this->translator->trans('slack.status.tribeTemplate'),
                $name,
            );
        }
        return $name;
    }
}
