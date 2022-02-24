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
use Divante\Bundle\AdventureBundle\Message\Agreement\UpdateAgreement;
use Doctrine\ORM\EntityManagerInterface;

class UpdateAgreementHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param UpdateAgreement $message
     * @throws \Exception
     */
    public function __invoke(UpdateAgreement $message) : void
    {
        $em = $this->em;
        $attachments = $this->getAttachments($message, $em);
        $agreement = $message->getEntry();
        $agreement->getAttachments()->clear();

        $em->getConnection()->beginTransaction();
        try {
            $agreement
                ->setNamePl($message->getNamePl())
                ->setNameEn($message->getNameEn())
                ->setDescriptionPl($message->getDescriptionPl())
                ->setDescriptionEn($message->getDescriptionEn())
                ->setRequired($message->isRequired())
                ->setPriority($message->getPriority())
                ->setType($message->getType())
                ->setContractIds($this->getContractIds($message))
                ->setUpdatedAt();
            foreach ($attachments as $attachment) {
                $agreement->getAttachments()->add($attachment);
            }
            $em->persist($agreement);
            $em->flush();
            $em->getConnection()->commit();
        } catch (\Exception $e) {
            $em->getConnection()->rollBack();
        }
    }

    /**
     * @param UpdateAgreement $message
     * @return int[]
     */
    private function getContractIds(UpdateAgreement $message) : array
    {
        if ($message->getType() === Agreement::TYPE_MARKETING) {
            return [];
        }
        return $message->getContracts();
    }

    /**
     * @param UpdateAgreement $message
     * @param EntityManagerInterface $em
     * @return AgreementAttachment[]
     */
    private function getAttachments(UpdateAgreement $message, EntityManagerInterface $em) : array
    {
        if ($message->getType() === Agreement::TYPE_MARKETING) {
            return [];
        } else {
            $attachments = [];
            foreach ($message->getAttachments() as $attachmentId) {
                /** @var AgreementAttachment|null $attachment */
                $attachment = $em->getRepository(AgreementAttachment::class)->find($attachmentId);
                if (!is_null($attachment)) {
                    $attachments[] = $attachment;
                }
            }
            return $attachments;
        }
    }
}
