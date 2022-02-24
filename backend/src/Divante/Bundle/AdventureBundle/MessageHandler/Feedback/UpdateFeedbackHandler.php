<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\MessageHandler\Feedback;

use Divante\Bundle\AdventureBundle\Entity\Feedback\Feedback;
use Divante\Bundle\AdventureBundle\Message\Feedback\UpdateFeedback;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpdateFeedbackHandler
{
    const TWO_HOURS = 7200;
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param UpdateFeedback $message
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function __invoke(UpdateFeedback $message) : void
    {
        $this->em->beginTransaction();
        $feedbackId = $message->getId();
        /** @var Feedback|null $feedback */
        $feedback = $this->em->getRepository(Feedback::class)->find($feedbackId);
        if (is_null($feedback)) {
            throw new NotFoundHttpException(sprintf("Feedback with ID %d not found", $feedbackId));
        }
        if ($message->getLeader() !== $feedback->getLeader()) {
            throw new NotFoundHttpException(sprintf(
                "You are not allowed to update feedback with ID %d",
                $feedbackId
            ));
        }
        try {
            $date = new \DateTime('@' . ($message->getDateCreated() + self::TWO_HOURS));
            $feedback
                ->setFeedback($message->getFeedback())
                ->setTechnicalFeedback($message->getTechnicalFeedback())
                ->setProgressFeedback($message->getProgressFeedback())
                ->setDateCreated($date)
                ->setUpdatedAt();
            $this->em->flush();
            $this->em->commit();
        } catch (Exception $e) {
            $this->em->rollback();
            throw new Exception($e);
        }
    }
}
