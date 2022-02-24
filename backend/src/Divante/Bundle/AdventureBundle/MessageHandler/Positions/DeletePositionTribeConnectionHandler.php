<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Positions;

use Divante\Bundle\AdventureBundle\Message\Positions\DeletePositionTribeConnection;

class DeletePositionTribeConnectionHandler extends AbstractPositionTribeHandler
{

    /**
     * @param DeletePositionTribeConnection $message
     * @throws \Exception
     */
    public function __invoke(DeletePositionTribeConnection $message) : void
    {
        $position = $this->getPosition($message->getPositionId());
        $tribe = $this->getTribe($message->getTribeId());
        if (!$tribe->getPositions()->contains($position) && !$position->getTribes()->contains($tribe)) {
            throw new \Exception("Pair doesn't exists");
        }
        $em = $this->entityManager;
        $em->beginTransaction();
        try {
            $index = $position->getTribes()->indexOf($tribe);
            $position->getTribes()->remove($index);
            $em->flush();
            $em->commit();
        } catch (\Exception $e) {
            $em->rollback();
            throw new \Exception($e);
        }
    }
}
