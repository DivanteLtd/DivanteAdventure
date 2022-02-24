<?php


namespace Divante\Bundle\AdventureBundle\MessageHandler\Dictionaries\Contract;

use Divante\Bundle\AdventureBundle\Entity\ContractType;
use Divante\Bundle\AdventureBundle\Message\Dictionaries\Contract\CreateContractType;
use Doctrine\ORM\EntityManagerInterface;

class CreateContractTypeHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param CreateContractType $message
     * @throws \Exception
     */
    public function __invoke(CreateContractType $message)
    {
        $this->em->beginTransaction();
        try {
            $contractType = (new ContractType())
                ->setCode($message->getCode())
                ->setName($message->getCode())
                ->setDescription($message->getDescription())
                ->setActive($message->isActive())
                ->setCreatedAt()
                ->setUpdatedAt();
            $this->em->persist($contractType);
            $this->em->flush();
            $this->em->commit();
        } catch (\Exception $e) {
            $this->em->rollback();
            throw $e;
        }
    }
}
