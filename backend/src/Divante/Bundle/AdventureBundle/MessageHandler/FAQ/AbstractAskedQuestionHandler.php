<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\FAQ;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\FAQ\FAQAskedQuestion;
use Divante\Bundle\AdventureBundle\Entity\FAQ\FAQAskedQuestion as Question;
use Divante\Bundle\AdventureBundle\Message\FAQ\AbstractAskedQuestionReaction as Message;
use Divante\Bundle\AdventureBundle\Representation\Email\EmailSenderInterface;
use Divante\Bundle\AdventureBundle\Services\Slack\MessageTemplates\FAQResponseMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Translation\TranslatorInterface;

abstract class AbstractAskedQuestionHandler
{
    private EntityManagerInterface $em;
    private EmailSenderInterface $emailSender;
    private TranslatorInterface $translator;
    private FAQResponseMessage $slackMessage;

    public function __construct(
        EntityManagerInterface $em,
        EmailSenderInterface $emailSender,
        TranslatorInterface $translator,
        FAQResponseMessage $slackMessage
    ) {
        $this->em = $em;
        $this->emailSender = $emailSender;
        $this->translator = $translator;
        $this->slackMessage = $slackMessage;
    }

    protected function handle(Message $message, ?string $rejectionReason) : void
    {
        $question = $this->getQuestion($message);
        $this->sendEmail($question, $message->getAnswerer(), $rejectionReason);
        $this->sendSlackMessage($message, $question, $rejectionReason);
        $this->deleteQuestion($question);
    }

    private function getQuestion(Message $message): Question
    {
        $id = $message->getQuestionId();
        $repo = $this->em->getRepository(FAQAskedQuestion::class);
        /** @var Question|null $question */
        $question = $repo->find($id);
        if (is_null($question)) {
            throw new NotFoundHttpException("Question with ID $id not found");
        }
        return $question;
    }

    private function sendEmail(Question $question, Employee $answerer, ?string $rejectionReason) : void
    {
        $accepted = !is_null($rejectionReason);
        $this->emailSender->sendMessage(
            $question->getQuestioner()->getEmail(),
            null,
            sprintf("[Adventure] %s", $this->getEmailTitlePart($accepted)),
            [
                'accepted' => $accepted,
                'answerer' => $answerer,
                'question' => $question,
                'rejectionReason' => $rejectionReason,
            ],
            'AdventureBundle:Emails:faq_message_response.html.twig'
        );
    }

    private function sendSlackMessage(Message $message, Question $question, ?string $rejectionReason) : void
    {
        $this->slackMessage->send($message->getAnswerer(), $question, $rejectionReason);
    }

    private function deleteQuestion(Question $question) : void
    {
        $this->em->remove($question);
        $this->em->flush();
    }

    private function getEmailTitlePart(bool $accepted) : string
    {
        if ($accepted) {
            return $this->translator->trans('yourQuestionHasBeenAccepted');
        } else {
            return $this->translator->trans('yourQuestionHasBeenRejected');
        }
    }
}
