<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Positions;

use Divante\Bundle\AdventureBundle\Entity\Level;
use Divante\Bundle\AdventureBundle\Entity\LevelingStrategy;
use Divante\Bundle\AdventureBundle\Message\Positions\CreateLevel;
use Doctrine\ORM\EntityManagerInterface;

class CreateLevelHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param CreateLevel $message
     * @throws \Exception
     */
    public function __invoke(CreateLevel $message) : void
    {
        $strategy = $this->getStrategy($message->getStrategyId());
        $name = $message->getName();
        $priority = $message->getPriority();

        $em = $this->em;
        $em->beginTransaction();
        try {
            $level = (new Level())
                ->setName($name)
                ->setStrategy($strategy)
                ->setPriority($priority)
                ->setCreatedAt()
                ->setUpdatedAt();
            $em->persist($level);
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
    private function getStrategy(int $strategyId) : LevelingStrategy
    {
        $em = $this->em;
        $repo = $em->getRepository(LevelingStrategy::class);
        /** @var LevelingStrategy|null $strategy */
        $strategy = $repo->find($strategyId);
        if (is_null($strategy)) {
            throw new \Exception("Strategy with ID $strategyId has not been found.");
        }
        return $strategy;
    }
}
