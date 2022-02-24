<?php
namespace Divante\Bundle\AdventureBundle\Message\Evidence;

use Divante\Bundle\AdventureBundle\Entity\Employee;

class OvertimeWithoutEvidence extends AbstractEvidenceMessage
{
    use OvertimeTrait;

    public const RESPONSE_WAITING_FOR_MANAGER_APPROVAL = "Waiting for manager approval";

    /**
     * OvertimeWithoutEvidence constructor.
     * @param Employee $employee
     * @param int $year
     * @param int $month
     * @param array<int,array<string,mixed>> $overtimeEntries
     * @param int $managerId
     */
    public function __construct(
        Employee $employee,
        int $year,
        int $month,
        array $overtimeEntries,
        int $managerId
    ) {
        parent::__construct($employee, $year, $month);
        $this->overtimeEntries = $overtimeEntries;
        $this->managerId = $managerId;
    }

    public function getSuccessfulResultMessage(): string
    {
        return self::RESPONSE_WAITING_FOR_MANAGER_APPROVAL;
    }

    public function shouldSendNotification(): bool
    {
        return true;
    }
}
