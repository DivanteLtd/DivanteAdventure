<?php
namespace Divante\Bundle\AdventureBundle\Message\Evidence;

use Divante\Bundle\AdventureBundle\Entity\Employee;

class EvidenceWithOvertime extends AbstractEvidenceMessage
{
    use OvertimeTrait, EvidenceTrait;

    public const RESPONSE_WAITING_FOR_MANAGER_APPROVAL = "Waiting for manager approval";

    /** @var int[] */
    private array $invoices;

    /**
     * EvidenceWithOvertime constructor.
     * @param Employee $employee
     * @param int $year
     * @param int $month
     * @param float $hours
     * @param int $paidDaysoffHours
     * @param int $unpaidDaysoffHours
     * @param int $sickLeaveHours
     * @param array<int,array<string,mixed>> $overtimeEntries
     * @param int $managerId
     * @param int[] $invoices
     */
    public function __construct(
        Employee $employee,
        int $year,
        int $month,
        float $hours,
        int $paidDaysoffHours,
        int $unpaidDaysoffHours,
        int $sickLeaveHours,
        array $overtimeEntries,
        int $managerId,
        array $invoices
    ) {
        parent::__construct($employee, $year, $month);
        $this->hours = $hours;
        $this->paidDaysoffHours = $paidDaysoffHours;
        $this->unpaidDaysoffHours = $unpaidDaysoffHours;
        $this->sickLeaveHours = $sickLeaveHours;
        $this->overtimeEntries = $overtimeEntries;
        $this->managerId = $managerId;
        $this->invoices = $invoices;
    }

    public function getSuccessfulResultMessage(): string
    {
        return self::RESPONSE_WAITING_FOR_MANAGER_APPROVAL;
    }

    /** @return int[] */
    public function getInvoiceIds() : array
    {
        return $this->invoices;
    }

    public function shouldSendNotification(): bool
    {
        return true;
    }
}
