<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Positions;

use Divante\Bundle\AdventureBundle\Message\Positions\CreatePositionTribeConnection;

class CreatePositionTribeConnectionHandler extends AbstractPositionTribeHandler
{

    /**
     * @param CreatePositionTribeConnection $message
     * @throws \Exception
     */
    public function __invoke(CreatePositionTribeConnection $message) : void
    {
        $position = $this->getPosition($message->getPositionId());
        $tribe = $this->getTribe($message->getTribeId());
        if ($tribe->getPositions()->contains($position) || $position->getTribes()->contains($tribe)) {
            throw new \Exception("Pair already exists");
        }
        $em = $this->entityManager;
        $em->beginTransaction();
        try {
            $position->getTribes()->add($tribe);
            $em->flush();
            $em->commit();
        } catch (\Exception $e) {
            $em->rollback();
            throw new \Exception($e);
        }
    }
}
