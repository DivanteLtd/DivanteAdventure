<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\MessageHandler\FAQ;

use Divante\Bundle\AdventureBundle\Entity\FAQ\FAQCategory;
use Divante\Bundle\AdventureBundle\Message\FAQ\DeleteFAQCategory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeleteFAQCategoryHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param DeleteFAQCategory $message
     * @throws \Exception
     * @throws NotFoundHttpException
     */
    public function __invoke(DeleteFAQCategory $message) : void
    {
        $this->em->beginTransaction();
        $id = $message->getId();
        /** @var FAQCategory|null $entry */
        $entry = $this->em->getRepository(FAQCategory::class)->find($id);
        if (is_null($entry)) {
            throw new NotFoundHttpException("FAQ Category with id $id not found.");
        }
        $entry->getEmployee()->clear();
        $this->em->remove($entry);
        $this->em->flush();
        $this->em->commit();
    }
}
