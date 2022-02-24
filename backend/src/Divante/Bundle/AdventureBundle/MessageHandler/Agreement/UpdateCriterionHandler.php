<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Agreement;

use Divante\Bundle\AdventureBundle\Entity\DataProcessingCriteria;
use Divante\Bundle\AdventureBundle\Message\Agreement\UpdateCriterion;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class UpdateCriterionHandler
{
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param UpdateCriterion $message
     * @throws Exception
     */
    public function __invoke(UpdateCriterion $message) : void
    {
        $id = $message->getId();
        $em = $this->em;
        /** @var DataProcessingCriteria|null $criterion */
        $criterion = $em->getRepository(DataProcessingCriteria::class)->find($id);
        if (is_null($criterion)) {
            throw new Exception("Position with ID {$id} has not been found");
        }

        $criterion
            ->setNamePl($message->getNamePl())
            ->setNameEn($message->getNameEn());
        $em->beginTransaction();
        try {
            $em->flush();
            $em->commit();
        } catch (Exception $e) {
            $em->rollback();
            throw new Exception($e);
        }
    }
}
