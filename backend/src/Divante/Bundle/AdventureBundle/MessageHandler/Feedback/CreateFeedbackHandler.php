<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\MessageHandler\Feedback;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Feedback\Feedback;
use Divante\Bundle\AdventureBundle\Message\Feedback\CreateFeedback;
use Divante\Bundle\AdventureBundle\Services\Slack\MessageTemplates\FeedbackCreatedMessage;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CreateFeedbackHandler
{
    private EntityManagerInterface $em;
    private FeedbackCreatedMessage $slackMessage;

    public function __construct(EntityManagerInterface $em, FeedbackCreatedMessage $slackMessage)
    {
        $this->em = $em;
        $this->slackMessage = $slackMessage;
    }

    /**
     * @param CreateFeedback $message
     * @throws NotFoundHttpException
     * @throws AccessDeniedHttpException
     * @throws Exception
     */
    public function __invoke(CreateFeedback $message) : void
    {
        $this->em->beginTransaction();

        $leader = $message->getLeader();
        $leaderId = $message->getLeader()->getId();
        $employeeId = $message->getEmployeeId();
        /** @var Employee|null $employee */
        $employee = $this->em->getRepository(Employee::class)->find($employeeId);
        if (is_null($employee)) {
            throw new NotFoundHttpException(sprintf("Employee with ID %d not found", $employeeId));
        }
        if (!$employee->isLeader($leaderId) && !$employee->getTribeResponsible()->contains($employee->getTribe())) {
            throw new AccessDeniedHttpException("You are not allowed to create feedback for this person");
        }
        try {
            $date = new \DateTime($message->getDateCreated());
            $feedback = (new Feedback())
                ->setEmployee($employee)
                ->setLeader($leader)
                ->setFeedback($message->getFeedback())
                ->setTechnicalFeedback($message->getTechnicalFeedback())
                ->setProgressFeedback($message->getProgressFeedback())
                ->setType($message->getType())
                ->setDateCreated($date)
                ->setCreatedAt()
                ->setUpdatedAt();
            $this->em->persist($feedback);
            $this->em->flush();
            $this->em->commit();
        } catch (Exception $e) {
            $this->em->rollback();
            throw $e;
        }
        $this->slackMessage->sendMessage($feedback);
    }
}
