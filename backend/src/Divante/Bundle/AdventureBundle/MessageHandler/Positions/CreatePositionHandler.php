<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Positions;

use Divante\Bundle\AdventureBundle\Entity\LevelingStrategy;
use Divante\Bundle\AdventureBundle\Entity\Position;
use Divante\Bundle\AdventureBundle\Message\Positions\CreatePosition;
use Doctrine\ORM\EntityManagerInterface;

class CreatePositionHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param CreatePosition $message
     * @throws \Exception
     */
    public function __invoke(CreatePosition $message) : void
    {
        $strategy = $this->getStrategy($message->getStrategyId());
        $name = $message->getName();

        if (is_null($strategy)) {
            throw new \Exception("Strategy with ID {$message->getStrategyId()} has not been found");
        }
        $em = $this->em;
        $em->beginTransaction();
        try {
            $position = (new Position())
                ->setName($name)
                ->setStrategy($strategy)
                ->setCreatedAt()
                ->setUpdatedAt();
            $em->persist($position);
            $em->flush();
            $em->commit();
        } catch (\Exception $e) {
            $em->rollback();
            throw new \Exception($e);
        }
    }

    private function getStrategy(int $id) : ?LevelingStrategy
    {
        $strategyRepo = $this->em->getRepository(LevelingStrategy::class);
        /** @var LevelingStrategy|null $strategy */
        $strategy = $strategyRepo->find($id);
        return $strategy;
    }
}
