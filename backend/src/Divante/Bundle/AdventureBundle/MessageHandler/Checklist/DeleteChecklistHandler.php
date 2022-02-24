<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\MessageHandler\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Checklist\Checklist;
use Divante\Bundle\AdventureBundle\Entity\FAQ\FAQCategory;
use Divante\Bundle\AdventureBundle\Message\Checklist\DeleteChecklist;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeleteChecklistHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param DeleteChecklist $message
     * @throws NotFoundHttpException
     */
    public function __invoke(DeleteChecklist $message) : void
    {
        $this->em->beginTransaction();
        $id = $message->getId();
        /** @var FAQCategory|null $entry */
        $entry = $this->em->getRepository(Checklist::class)->find($id);
        if (is_null($entry)) {
            throw new NotFoundHttpException("Checklist with id $id not found.");
        }
        $this->em->remove($entry);
        $this->em->flush();
        $this->em->commit();
    }
}
