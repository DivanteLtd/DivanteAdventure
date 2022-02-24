<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Positions;

use Divante\Bundle\AdventureBundle\Entity\LevelingStrategy;
use Divante\Bundle\AdventureBundle\Message\Positions\DeleteLevelingStrategy;
use Doctrine\ORM\EntityManagerInterface;

class DeleteLevelingStrategyHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param DeleteLevelingStrategy $message
     * @throws \Exception
     */
    public function __invoke(DeleteLevelingStrategy $message) : void
    {
        $strategy = $this->getLevelingStrategy($message->getDeleteId());
        if (!$strategy->getPositions()->isEmpty()) {
            throw new \Exception("There are positions using this strategy. Delete or update them first");
        }
        if (!$strategy->getLevels()->isEmpty()) {
            throw new \Exception("There are levels using this strategy. Delete or update them first");
        }

        $em = $this->em;
        $em->beginTransaction();
        try {
            $em->remove($strategy);
            $em->flush();
            $em->commit();
        } catch (\Exception $e) {
            $em->rollback();
            throw new \Exception($e);
        }
    }

    /**
     * @param int $strategyId
     * @return LevelingStrategy
     * @throws \Exception
     */
    private function getLevelingStrategy(int $strategyId) : LevelingStrategy
    {
        $em = $this->em;
        $repo = $em->getRepository(LevelingStrategy::class);
        /** @var LevelingStrategy|null $strategy */
        $strategy = $repo->find($strategyId);
        if (is_null($strategy)) {
            throw new \Exception("Strategy with ID $strategyId has not been found");
        }
        return $strategy;
    }
}
