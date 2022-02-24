<?php
namespace Divante\Bundle\AdventureBundle\MessageHandler\Evidence;

use Divante\Bundle\AdventureBundle\Entity\Evidence;
use Divante\Bundle\AdventureBundle\Entity\EvidenceInvoice;
use Divante\Bundle\AdventureBundle\Events\Evidence\EvidenceEvent;
use Divante\Bundle\AdventureBundle\Message\Evidence\EvidenceWithoutOvertime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EvidenceWithoutOvertimeHandler
{
    private EntityManagerInterface $em;
    private EventDispatcherInterface $dispatcher;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $dispatcher)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param EvidenceWithoutOvertime $message
     * @throws Exception
     */
    public function __invoke(EvidenceWithoutOvertime $message) : void
    {
        $em = $this->em;
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
            ->setSickLeaveHours((string)$message->getSickLeaveHours())
            ->setUnavailabilityHours((string)$message->getUnavailabilityHours());

        $this->updateInvoices($message, $evidence);

        $this->dispatcher->dispatch(new EvidenceEvent($evidence));
        $evidence->setEvidenceStatus(Evidence::STATUS_EVIDENCE_SENT);

        $em->persist($evidence);
        $em->flush();
    }

    /**
     * @param EvidenceWithoutOvertime $message
     * @param Evidence $evidence
     * @throws Exception
     */
    private function updateInvoices(EvidenceWithoutOvertime $message, Evidence $evidence) : void
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
