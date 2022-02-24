<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 * Date: 26.03.19
 * Time: 08:29
 */

namespace Divante\Bundle\AdventureBundle\MessageHandler\Agreement;

use Divante\Bundle\AdventureBundle\Entity\Agreement;
use Divante\Bundle\AdventureBundle\Entity\AgreementAttachment;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Message\Agreement\CreateAgreement;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class CreateAgreementHandler
{
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param CreateAgreement $message
     * @throws Exception
     */
    public function __invoke(CreateAgreement $message) : void
    {
        $em = $this->em;
        $attachments = $this->getAttachments($message);

        $em->getConnection()->beginTransaction();
        try {
            $contracts = [];
            if ($message->getType() === Agreement::TYPE_MARKETING) {
                $contracts = [
                    Employee::CONTRACT_B2B_LUMP_SUM,
                    Employee::CONTRACT_B2B_HOURLY,
                    Employee::CONTRACT_CLC_LUMP_SUM,
                    Employee::CONTRACT_CLC_HOURLY,
                    Employee::CONTRACT_COE
                ];
            }
            $agreement = new Agreement();
            $agreement
                ->setNamePl($message->getNamePl())
                ->setNameEn($message->getNameEn())
                ->setDescriptionPl($message->getDescriptionPl())
                ->setDescriptionEn($message->getDescriptionEn())
                ->setRequired($message->isRequired())
                ->setPriority($message->getPriority())
                ->setType($message->getType())
                ->setCreatedAt()
                ->setUpdatedAt()
                ->setContractIds($this->getContractIds($message) ? $this->getContractIds($message) : $contracts);
            foreach ($attachments as $attachment) {
                $agreement->getAttachments()->add($attachment);
            }
            $em->persist($agreement);
            $em->flush();
            $em->getConnection()->commit();
        } catch (Exception $e) {
            $em->getConnection()->rollBack();
        }
    }

    /**
     * @param CreateAgreement $message
     * @return int[]
     */
    private function getContractIds(CreateAgreement $message) : array
    {
        if ($message->getType() === Agreement::TYPE_GDPR
            || $message->getType() === Agreement::TYPE_FIRE_SAFETY
            || $message->getType() === Agreement::TYPE_ISO) {
            return $message->getContracts();
        } else {
            return [];
        }
    }

    /**
     * @param CreateAgreement $message
     * @return AgreementAttachment[]
     */
    private function getAttachments(CreateAgreement $message) : array
    {
        /** @var AgreementAttachment[] $attachments */
        $attachments = [];
        if ($message->getType() === Agreement::TYPE_GDPR
            || $message->getType() === Agreement::TYPE_FIRE_SAFETY
            || $message->getType() === Agreement::TYPE_ISO) {
            foreach ($message->getAttachments() as $attachmentId) {
                /** @var AgreementAttachment|null $attachment */
                $attachment = $this->em->getRepository(AgreementAttachment::class)->find($attachmentId);
                if (!is_null($attachment)) {
                    $attachments[] = $attachment;
                }
            }
            return $attachments;
        } else {
            return $attachments;
        }
    }
}
