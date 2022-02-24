<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Positions;

use Divante\Bundle\AdventureBundle\Entity\Level;
use Divante\Bundle\AdventureBundle\Entity\LevelingStrategy;
use Divante\Bundle\AdventureBundle\Message\Positions\UpdateLevel;
use Doctrine\ORM\EntityManagerInterface;

class UpdateLevelHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param UpdateLevel $message
     * @throws \Exception
     */
    public function __invoke(UpdateLevel $message) : void
    {
        $level = $this->getUpdatedLevel($message->getUpdatedLevelId());
        $em = $this->em;
        $em->beginTransaction();
        try {
            $level
                ->setStrategy($this->getStrategy($message, $level))
                ->setName($this->getName($message, $level))
                ->setPriority($this->getPriority($message, $level))
                ->setUpdatedAt();
            $em->flush();
            $em->commit();
        } catch (\Exception $e) {
            $em->rollback();
            throw new \Exception($e);
        }
    }

    /**
     * @param int $levelId
     * @return Level
     * @throws \Exception
     */
    private function getUpdatedLevel(int $levelId) : Level
    {
        $repo = $this->em->getRepository(Level::class);
        /** @var Level|null $level */
        $level = $repo->find($levelId);
        if (is_null($level)) {
            throw new \Exception("Level with ID $levelId has not been found");
        }
        return $level;
    }

    private function getName(UpdateLevel $message, Level $level) : string
    {
        return $message->getName() ?? $level->getName();
    }

    private function getPriority(UpdateLevel $message, Level $level) : int
    {
        return $message->getPriority() ?? $level->getPriority();
    }

    /**
     * @param UpdateLevel $message
     * @param Level $level
     * @return LevelingStrategy
     * @throws \Exception
     */
    private function getStrategy(UpdateLevel $message, Level $level) : LevelingStrategy
    {
        $strategyId = $message->getStrategyId();
        if (is_null($strategyId)) {
            return $level->getStrategy();
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
