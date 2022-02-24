<?php
namespace Divante\Bundle\AdventureBundle\MessageHandler\Evidence;

use Divante\Bundle\AdventureBundle\Entity\Evidence;
use Divante\Bundle\AdventureBundle\Entity\EvidenceInvoice;
use Divante\Bundle\AdventureBundle\Entity\EvidenceOvertime;
use Divante\Bundle\AdventureBundle\Events\Evidence\EvidenceEvent;
use Divante\Bundle\AdventureBundle\Events\Evidence\OvertimeEvent;
use Divante\Bundle\AdventureBundle\Message\Evidence\EvidenceWithOvertime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EvidenceWithOvertimeHandler
{

    public const RESPONSE_MANAGER_NOT_FOUND = "Manager with given ID was not found";

    private EntityManagerInterface $em;
    private EventDispatcherInterface $dispatcher;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $dispatcher)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param EvidenceWithOvertime $message
     * @throws Exception
     */
    public function __invoke(EvidenceWithOvertime $message) : void
    {
        $em = $this->em;

        $manager = $message->tryFindManager($em);
        if (is_null($manager)) {
            throw new Exception(self::RESPONSE_MANAGER_NOT_FOUND);
        }

        $evidence = $message->tryFindExistingEvidence($em);
        if (!is_null($evidence)) {
            $em->remove($evidence);
            $em->flush();
        }

        $evidence = new Evidence();
        $evidence
            ->setCreatedAt()
            ->setUpdatedAt()
            ->setEmployee($message->getEmployee())
            ->setYear($message->getYear())
            ->setMonth($message->getMonth())
            ->setWorkingHours((string)$message->getHours())
            ->setPaidFreeHours((string)$message->getPaidDaysoffHours())
            ->setUnpaidFreeHours((string)$message->getUnpaidDaysoffHours())
            ->setOvertimeManager($manager)
            ->setOvertimeStatus(Evidence::STATUS_OVERTIME_AWAITS_APPROVAL)
            ->setSickLeaveHours((string)$message->getSickLeaveHours())
            ->setUnavailabilityHours("0");
        $em->persist($evidence);

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

        $this->updateInvoices($message, $evidence);

        $this->dispatcher->dispatch(new EvidenceEvent($evidence));
        $this->dispatcher->dispatch(new OvertimeEvent($evidence));
        $evidence->setEvidenceStatus(Evidence::STATUS_EVIDENCE_SENT);
    }

    /**
     * @param EvidenceWithOvertime $message
     * @param Evidence $evidence
     * @throws Exception
     */
    private function updateInvoices(EvidenceWithOvertime $message, Evidence $evidence) : void
    {
        $invoicesRepo = $this->em->getRepository(EvidenceInvoice::class);
        /** @var int $invoiceId */
        foreach ($message->getInvoiceIds() as $invoiceId) {
            /** @var EvidenceInvoice|null $invoice */
            $invoice = $invoicesRepo->find($invoiceId);
            if (is_null($invoice)) {
                throw new Exception("Invoice with ID $invoiceId does not exists");
            }
            if ($invoice->getOwner()->getId() !== $message->getEmployee()->getId()) {
                throw new Exception("User is not an owner of invoice with ID $invoiceId");
            }
            if (!is_null($invoice->getEvidence())) {
                throw new Exception("Invoice with ID $invoiceId is already used");
            }
            $invoice->setEvidence($evidence);
        }
    }
}
