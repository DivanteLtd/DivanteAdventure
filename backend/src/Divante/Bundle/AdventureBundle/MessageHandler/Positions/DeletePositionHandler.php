<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Positions;

use Divante\Bundle\AdventureBundle\Entity\Position;
use Divante\Bundle\AdventureBundle\Message\Positions\DeletePosition;
use Doctrine\ORM\EntityManagerInterface;

class DeletePositionHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param DeletePosition $message
     * @throws \Exception
     */
    public function __invoke(DeletePosition $message) : void
    {
        /** @var Position $deletedPosition */
        $deletedPosition = $this->getPosition($message->getPositionId());
        if (!$deletedPosition->getEmployees()->isEmpty()) {
            throw new \Exception("Given position is not empty");
        }

        $em = $this->em;
        $em->beginTransaction();
        try {
            $em->remove($deletedPosition);
            $em->flush();
            $em->commit();
        } catch (\Exception $e) {
            $em->rollback();
            throw new \Exception($e);
        }
    }

    /**
     * @param int $id
     * @return Position
     * @throws \Exception
     */
    private function getPosition(int $id) : Position
    {
        $repo = $this->em->getRepository(Position::class);
        /** @var Position|null $position */
        $position = $repo->find($id);
        if (is_null($position)) {
            throw new \Exception("Position with ID $id has not been found");
        }
        return $position;
    }
}
