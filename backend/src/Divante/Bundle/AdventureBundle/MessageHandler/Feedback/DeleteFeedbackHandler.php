<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\MessageHandler\Feedback;

use Divante\Bundle\AdventureBundle\Entity\Feedback\Feedback;
use Divante\Bundle\AdventureBundle\Message\Feedback\DeleteFeedback;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeleteFeedbackHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param DeleteFeedback $message
     * @throws \Exception
     * @throws NotFoundHttpException
     */
    public function __invoke(DeleteFeedback $message) : void
    {
        $this->em->beginTransaction();
        $feedbackId = $message->getId();
        /** @var Feedback|null $entry */
        $entry = $this->em->getRepository(Feedback::class)->find($feedbackId);
        if (is_null($entry)) {
            throw new NotFoundHttpException(sprintf("Feedback with ID %d not found", $feedbackId));
        }
        if ($message->getEmployee() !== $entry->getLeader()) {
            throw new NotFoundHttpException(sprintf(
                "You are not allowed to delete feedback with ID %d",
                $feedbackId
            ));
        }
        $this->em->remove($entry);
        $this->em->flush();
        $this->em->commit();
    }
}
