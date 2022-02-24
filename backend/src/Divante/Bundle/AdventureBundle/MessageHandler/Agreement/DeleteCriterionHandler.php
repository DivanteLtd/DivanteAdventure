<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Agreement;

use Divante\Bundle\AdventureBundle\Entity\DataProcessingCriteria;
use Divante\Bundle\AdventureBundle\Message\Agreement\DeleteCriterion;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class DeleteCriterionHandler
{
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param DeleteCriterion $message
     * @throws Exception
     */
    public function __invoke(DeleteCriterion $message) : void
    {
        $id = $message->getId();
        $em = $this->em;
        $criterion = $em->getRepository(DataProcessingCriteria::class)->find($id);
        $em->beginTransaction();
        try {
            $em->remove($criterion);
            $em->flush();
            $em->commit();
        } catch (Exception $e) {
            $em->rollback();
            throw new Exception($e);
        }
    }
}
