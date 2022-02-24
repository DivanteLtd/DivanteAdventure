<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Positions;

use Divante\Bundle\AdventureBundle\Entity\LevelingStrategy;
use Divante\Bundle\AdventureBundle\Entity\Position;
use Divante\Bundle\AdventureBundle\Message\Positions\UpdatePosition;
use Doctrine\ORM\EntityManagerInterface;

class UpdatePositionHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param UpdatePosition $message
     * @throws \Exception
     */
    public function __invoke(UpdatePosition $message) : void
    {
        $position = $this->getUpdatedPosition($message->getUpdatedPositionId());
        if (is_null($position)) {
            throw new \Exception("Position with ID {$message->getUpdatedPositionId()} has not been found");
        }

        $position
            ->setName($this->getPositionName($message, $position))
            ->setStrategy($this->getStrategy($message, $position))
            ->setUpdatedAt();

        $em = $this->em;
        $em->beginTransaction();
        try {
            $em->flush();
            $em->commit();
        } catch (\Exception $e) {
            $em->rollback();
            throw new \Exception($e);
        }
    }

    private function getUpdatedPosition(int $id) : ?Position
    {
        $repo = $this->em->getRepository(Position::class);
        /** @var Position|null $position */
        $position = $repo->find($id);
        return $position;
    }

    private function getPositionName(UpdatePosition $message, Position $position) : string
    {
        return $message->getNewName() ?? $position->getName();
    }

    /**
     * @param UpdatePosition $message
     * @param Position $position
     * @return LevelingStrategy
     * @throws \Exception
     */
    private function getStrategy(UpdatePosition $message, Position $position) : LevelingStrategy
    {
        $strategyId = $message->getNewStrategyId();
        if (is_null($strategyId)) {
            return $position->getStrategy();
        }

        $repo = $this->em->getRepository(LevelingStrategy::class);
        /** @var LevelingStrategy|null $strategy */
        $strategy = $repo->find($strategyId);
        if (is_null($strategy)) {
            throw new \Exception("Strategy with ID $strategyId has not been found");
        }
        return $strategy;
    }
}
