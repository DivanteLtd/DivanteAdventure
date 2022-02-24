<?php


namespace Divante\Bundle\AdventureBundle\MessageHandler\Dictionaries\Contract;

use Divante\Bundle\AdventureBundle\Entity\ContractType;
use Divante\Bundle\AdventureBundle\Message\Dictionaries\Contract\UpdateContractType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpdateContractTypeHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param UpdateContractType $message
     * @throws \Exception
     */
    public function __invoke(UpdateContractType $message)
    {
        $this->em->beginTransaction();
        /** @var ContractType|null $contractType */
        $contractType = $this->em->getRepository(ContractType::class)->find($message->getId());
        if (is_null($contractType)) {
            throw new NotFoundHttpException('Contract type not found');
        }
        try {
            $contractType
                ->setName($message->getName())
                ->setDescription($message->getDescription())
                ->setActive($message->isActive());
            $this->em->persist($contractType);
            $this->em->flush();
            $this->em->commit();
        } catch (\Exception $exception) {
            $this->em->rollback();
            throw $exception;
        }
    }
}
