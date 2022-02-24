<?php
namespace Divante\Bundle\AdventureBundle\Message\Evidence;

use Divante\Bundle\AdventureBundle\Entity\Employee;

class EvidenceWithoutOvertime extends AbstractEvidenceMessage
{
    public const RESPONSE_EVIDENCE_SENT = "Evidence sent";

    use EvidenceTrait;

    /** @var int[] */
    private array $invoices;
    /** @var int */
    private int $unavailabilityHours;

    /**
     * EvidenceWithoutOvertime constructor.
     * @param Employee $employee
     * @param int $year
     * @param int $month
     * @param float $hours
     * @param int $paidDaysoffHours
     * @param int $unpaidDaysoffHours
     * @param int $sickLeaveHours
     * @param int $unavailabilityHours
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
        int $unavailabilityHours,
        array $invoices
    ) {
        parent::__construct($employee, $year, $month);
        $this->hours = $hours;
        $this->paidDaysoffHours = $paidDaysoffHours;
        $this->unpaidDaysoffHours = $unpaidDaysoffHours;
        $this->sickLeaveHours = $sickLeaveHours;
        $this->unavailabilityHours = $unavailabilityHours;
        $this->invoices = $invoices;
    }

    public function getSuccessfulResultMessage(): string
    {
        return self::RESPONSE_EVIDENCE_SENT;
    }

    /** @return int[] */
    public function getInvoiceIds() : array
    {
        return $this->invoices;
    }

    public function shouldSendNotification(): bool
    {
        return false;
    }

    public function getUnavailabilityHours() : int
    {
        return $this->unavailabilityHours;
    }
}
