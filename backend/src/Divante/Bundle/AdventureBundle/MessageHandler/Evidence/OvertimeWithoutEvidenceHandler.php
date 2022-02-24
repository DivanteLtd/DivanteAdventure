<?php
namespace Divante\Bundle\AdventureBundle\MessageHandler\Evidence;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Evidence;
use Divante\Bundle\AdventureBundle\Entity\EvidenceOvertime;
use Divante\Bundle\AdventureBundle\Events\Evidence\EvidenceEvent;
use Divante\Bundle\AdventureBundle\Events\Evidence\OvertimeEvent;
use Divante\Bundle\AdventureBundle\Message\Evidence\OvertimeWithoutEvidence;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class OvertimeWithoutEvidenceHandler
{
    public const RESPONSE_OVERTIME_NON_EXISTING_EVIDENCE = "Tried to send overtime but evidence doesn't exists";
    public const RESPONSE_MANAGER_NOT_FOUND = "Manager with given ID was not found";

    private EntityManagerInterface $em;
    private EventDispatcherInterface $dispatcher;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $dispatcher)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param OvertimeWithoutEvidence $message
     * @throws \Exception
     */
    public function __invoke(OvertimeWithoutEvidence $message) : void
    {
        $em = $this->em;
        $evidence = $message->tryFindExistingEvidence($em);
        $employeeContract = $message->getEmployee()->getContractId();
        if (is_null($evidence) && $employeeContract !== Employee::CONTRACT_COE) {
            throw new \Exception(self::RESPONSE_OVERTIME_NON_EXISTING_EVIDENCE);
        }

        $manager = $message->tryFindManager($em);
        if (is_null($manager)) {
            throw new \Exception(self::RESPONSE_MANAGER_NOT_FOUND);
        }
        if (!is_null($evidence)) {
            foreach ($evidence->getOvertimeEntries() as $overtimeEntry) {
                $em->remove($overtimeEntry);
            }
            $evidence->getOvertimeEntries()->clear();
            $evidence->setUpdatedAt();
            $em->flush();
        } else {
            $evidence = (new Evidence())
                ->setCreatedAt()
                ->setUpdatedAt()
                ->setEmployee($message->getEmployee())
                ->setYear($message->getYear())
                ->setMonth($message->getMonth())
                ->setWorkingHours("0")
                ->setPaidFreeHours("0")
                ->setUnpaidFreeHours("0")
                ->setSickLeaveHours("0")
                ->setUnavailabilityHours("0");
            $em->persist($evidence);
        }

        $evidence
            ->setOvertimeManager($manager)
            ->setOvertimeStatus(Evidence::STATUS_OVERTIME_AWAITS_APPROVAL);
        foreach ($message->getOvertimeEntries() as $overtimeEntry) {
            $entry = (new EvidenceOvertime())
                ->setEvidence($evidence)
                ->setProjectName($overtimeEntry['project'])
                ->setProjectCode($overtimeEntry['code'])
                ->setHours($overtimeEntry['hours'])
                ->setPercentage($overtimeEntry['percent'])
                ->setTimeInfo($overtimeEntry['date']);
            $em->persist($entry);
        }
        $em->flush();
        $this->dispatcher->dispatch(new OvertimeEvent($evidence));
    }
}
