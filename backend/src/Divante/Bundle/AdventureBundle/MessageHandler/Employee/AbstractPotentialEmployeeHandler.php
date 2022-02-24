<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Employee;

use Divante\Bundle\AdventureBundle\Entity\Position;
use Divante\Bundle\AdventureBundle\Entity\Tribe;
use Divante\Bundle\AdventureBundle\Message\Employee\AbstractPotentialEmployeeMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractPotentialEmployeeHandler
{
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $em)
    {
        $this->entityManager = $em;
    }

    /**
     * @param AbstractPotentialEmployeeMessage $message
     * @return \DateTime|null
     * @throws \Exception
     */
    protected function getHireDate(AbstractPotentialEmployeeMessage $message) : ?\DateTime
    {
        $hireDate = $message->getDesignatedHireDate();
        if (is_null($hireDate)) {
            return null;
        } elseif (!preg_match('/\d{4}-\d{2}-\d{2}/', $hireDate)) {
            throw new \Exception(
                '"hireDate" must be either "null" or in YYYY-MM-DD format.',
                Response::HTTP_BAD_REQUEST
            );
        } else {
            return \DateTime::createFromFormat('Y-m-d', $hireDate);
        }
    }

    /**
     * @param AbstractPotentialEmployeeMessage $message
     * @return \DateTime|null
     * @throws \Exception
     */
    protected function getDateOfBirthDate(AbstractPotentialEmployeeMessage $message) : ?\DateTime
    {
        $dateOfBirth = $message->getDateOfBirth();
        if (is_null($dateOfBirth)) {
            return null;
        } elseif (!preg_match('/\d{4}-\d{2}-\d{2}/', $dateOfBirth)) {
            throw new \Exception(
                '"dateOfBirth" must be either "null" or in YYYY-MM-DD format.',
                Response::HTTP_BAD_REQUEST
            );
        } else {
            return \DateTime::createFromFormat('Y-m-d', $dateOfBirth);
        }
    }

    /**
     * @param AbstractPotentialEmployeeMessage $message
     * @return Tribe|null
     * @throws \Exception
     */
    protected function getTribe(AbstractPotentialEmployeeMessage $message) : ?Tribe
    {
        $tribeId = $message->getDesignatedTribeId();
        if (is_null($tribeId)) {
            return null;
        }
        $repository = $this->entityManager->getRepository(Tribe::class);
        /** @var Tribe|null $tribe */
        $tribe = $repository->find($tribeId);
        if (!is_null($tribe)) {
            return $tribe;
        } else {
            throw new \Exception("Tribe with ID $tribeId has not been found", Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param AbstractPotentialEmployeeMessage $message
     * @return Position|null
     * @throws \Exception
     */
    protected function getPosition(AbstractPotentialEmployeeMessage $message) : ?Position
    {
        $positionId = $message->getDesignatedPositionId();
        if (is_null($positionId)) {
            return null;
        }
        $repository = $this->entityManager->getRepository(Position::class);
        /** @var Position|null $position */
        $position = $repository->find($positionId);
        if (!is_null($position)) {
            return $position;
        } else {
            throw new \Exception("Position with ID $positionId has not been found", Response::HTTP_BAD_REQUEST);
        }
    }
}
