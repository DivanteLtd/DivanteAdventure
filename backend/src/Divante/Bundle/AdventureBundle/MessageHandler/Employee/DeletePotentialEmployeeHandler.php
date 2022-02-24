<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Employee;

use Divante\Bundle\AdventureBundle\Entity\PotentialEmployee;
use Divante\Bundle\AdventureBundle\Message\Employee\DeletePotentialEmployee;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class DeletePotentialEmployeeHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param DeletePotentialEmployee $message
     * @throws \Exception
     */
    public function __invoke(DeletePotentialEmployee $message) : void
    {
        $id = $message->getId();
        $repo = $this->em->getRepository(PotentialEmployee::class);
        /** @var PotentialEmployee|null $employee */
        $employee = $repo->find($id);
        if (is_null($employee)) {
            throw new \Exception("Employee with ID $id has not been found", Response::HTTP_NOT_FOUND);
        } else {
            try {
                $this->em->remove($employee);
                $this->em->flush();
            } catch (\Exception $e) {
                throw new \Exception(
                    "Error while removing employee: ".$e->getMessage(),
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }
        }
    }
}
