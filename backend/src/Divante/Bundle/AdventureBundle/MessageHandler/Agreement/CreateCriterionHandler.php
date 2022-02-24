<?php


namespace Divante\Bundle\AdventureBundle\MessageHandler\Agreement;

use Divante\Bundle\AdventureBundle\Entity\DataProcessingCriteria;
use Divante\Bundle\AdventureBundle\Message\Agreement\CreateCriterion;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class CreateCriterionHandler
{
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param CreateCriterion $message
     * @throws Exception
     */
    public function __invoke(CreateCriterion $message) : void
    {
        $repo = $this->em->getRepository(DataProcessingCriteria::class);
        /** @var DataProcessingCriteria|null $criterion */
        $criterion = $repo->findOneBy([
            'namePl' => $message->getNamePl()
        ]);
        if (!is_null($criterion)) {
            throw new Exception("Criterion with this name already exist.");
        }
        try {
            $criterion = new DataProcessingCriteria();
            $criterion->setNamePl($message->getNamePl());
            $criterion->setNameEn($message->getNameEn());
            $this->em->persist($criterion);
            $this->em->flush();
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }
}
