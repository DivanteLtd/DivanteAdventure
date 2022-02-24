<?php
namespace Divante\Bundle\AdventureBundle\Services;

use Divante\Bundle\AdventureBundle\Message\Evidence\AbstractEvidenceMessage;
use Divante\Bundle\AdventureBundle\Message\Evidence\EvidenceWithoutOvertime;
use Divante\Bundle\AdventureBundle\Message\Evidence\EvidenceWithOvertime;
use Divante\Bundle\AdventureBundle\Message\Evidence\OvertimeWithoutEvidence;
use Divante\Bundle\AdventureBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;

class EvidenceMessageFactory
{
    public const ERROR_OVERTIME_ONLY_WITHOUT_OVERTIME
        = "'Overtime only' is selected, but overtime entries and overtime manager have not been sent";

    /**
     * @param User $user
     * @param Request $request
     * @return AbstractEvidenceMessage
     * @throws \Exception
     */
    public function getMessage(User $user, Request $request) : AbstractEvidenceMessage
    {
        $year = $request->get('year');
        $month = $request->get('month');
        $overtimeEntries = $request->get('overtime', []);
        $overtimeManagerId = $request->get('manager', -1);
        $invoicesIds = $request->get('invoices', []);

        $overtimeOnly = $request->get('overtimeOnly');
        $overtimeSent = count($overtimeEntries) > 0 && $overtimeManagerId !== -1;

        if (!$overtimeOnly && !$overtimeSent) {
            return new EvidenceWithoutOvertime(
                $user->getEmployee(),
                $year,
                $month,
                $request->get('hours'),
                $request->get('paidDaysoffHours', 0),
                $request->get('unpaidDaysoffHours', 0),
                $request->get('sickLeaveHours', 0),
                $request->get('unavailabilityHours', 0),
                $invoicesIds
            );
        } elseif (!$overtimeOnly && $overtimeSent) {
            return new EvidenceWithOvertime(
                $user->getEmployee(),
                $year,
                $month,
                $request->get('hours'),
                $request->get('paidDaysoffHours', 0),
                $request->get('unpaidDaysoffHours', 0),
                $request->get('sickLeaveHours', 0),
                $overtimeEntries,
                $overtimeManagerId,
                $invoicesIds
            );
        } elseif ($overtimeOnly && $overtimeSent) {
            return new OvertimeWithoutEvidence(
                $user->getEmployee(),
                $year,
                $month,
                $overtimeEntries,
                $overtimeManagerId
            );
        } else {
            throw new \Exception(self::ERROR_OVERTIME_ONLY_WITHOUT_OVERTIME);
        }
    }
}
