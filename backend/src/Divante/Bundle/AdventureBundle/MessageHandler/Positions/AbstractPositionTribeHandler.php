<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Positions;

use Divante\Bundle\AdventureBundle\Entity\Position;
use Divante\Bundle\AdventureBundle\Entity\Tribe;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractPositionTribeHandler
{
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $em)
    {
        $this->entityManager = $em;
    }

    /**
     * @param int $positionId
     * @return Position
     * @throws \Exception
     */
    protected function getPosition(int $positionId) : Position
    {
        $repo = $this->entityManager->getRepository(Position::class);
        /** @var Position|null $position */
        $position = $repo->find($positionId);
        if (is_null($position)) {
            throw new \Exception("Position with ID $positionId has not been found");
        }
        return $position;
    }

    /**
     * @param int $tribeId
     * @return Tribe
     * @throws \Exception
     */
    protected function getTribe(int $tribeId) : Tribe
    {
        $repo = $this->entityManager->getRepository(Tribe::class);
        /** @var Tribe|null $tribe */
        $tribe = $repo->find($tribeId);
        if (is_null($tribe)) {
            throw new \Exception("Tribe with ID $tribeId has not been found");
        }
        return $tribe;
    }
}
