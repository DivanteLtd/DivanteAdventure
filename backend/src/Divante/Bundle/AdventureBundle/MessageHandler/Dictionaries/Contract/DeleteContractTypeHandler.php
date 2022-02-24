<?php


namespace Divante\Bundle\AdventureBundle\MessageHandler\Dictionaries\Contract;

use Divante\Bundle\AdventureBundle\Entity\ContractType;
use Divante\Bundle\AdventureBundle\Message\Dictionaries\Contract\DeleteContractType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeleteContractTypeHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(DeleteContractType $message)
    {
        $this->em->beginTransaction();
        /** @var ContractType|null $contractType */
        $contractType = $this->em->getRepository(ContractType::class)->find($message->getId());
        if (is_null($contractType)) {
            throw new NotFoundHttpException('Contract type not found');
        }
        $this->em->remove($contractType);
        $this->em->flush();
        $this->em->commit();
    }
}
