<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Positions;

use Divante\Bundle\AdventureBundle\Entity\LevelingStrategy;
use Divante\Bundle\AdventureBundle\Message\Positions\UpdateLevelingStrategy;
use Doctrine\ORM\EntityManagerInterface;

class UpdateLevelingStrategyHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param UpdateLevelingStrategy $message
     * @throws \Exception
     */
    public function __invoke(UpdateLevelingStrategy $message) : void
    {
        $strategy = $this->getLevelingStrategy($message->getUpdateId());
        $name = $message->getName() ?? $strategy->getName();
        $em = $this->em;

        $em->beginTransaction();
        try {
            $strategy
                ->setName($name)
                ->setUpdatedAt();
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
        $repo = $this->em->getRepository(LevelingStrategy::class);
        /** @var LevelingStrategy|null $strategy */
        $strategy = $repo->find($strategyId);
        if (is_null($strategy)) {
            throw new \Exception("Strategy with ID $strategyId has not been found");
        }
        return $strategy;
    }
}
