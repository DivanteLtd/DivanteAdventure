<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Positions;

use Divante\Bundle\AdventureBundle\Entity\LevelingStrategy;
use Divante\Bundle\AdventureBundle\Message\Positions\CreateLevelingStrategy;
use Doctrine\ORM\EntityManagerInterface;

class CreateLevelingStrategyHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param CreateLevelingStrategy $message
     * @throws \Exception
     */
    public function __invoke(CreateLevelingStrategy $message) : void
    {
        $em = $this->em;
        $em->beginTransaction();
        try {
            $levelingStrategy = (new LevelingStrategy())
                ->setName($message->getName())
                ->setCreatedAt()
                ->setUpdatedAt();
            $em->persist($levelingStrategy);
            $em->flush();
            $em->commit();
        } catch (\Exception $e) {
            $em->rollback();
            throw new \Exception($e);
        }
    }
}
