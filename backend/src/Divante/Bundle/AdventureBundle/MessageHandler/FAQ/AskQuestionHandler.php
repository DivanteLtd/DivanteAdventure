<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\FAQ;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\FAQ\FAQAskedQuestion;
use Divante\Bundle\AdventureBundle\Entity\FAQ\FAQCategory;
use Divante\Bundle\AdventureBundle\Message\FAQ\AskQuestion;
use Divante\Bundle\AdventureBundle\Representation\Email\EmailSenderInterface;
use Divante\Bundle\AdventureBundle\Services\Slack\MessageTemplates\FAQMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Translation\TranslatorInterface;

class AskQuestionHandler
{
    private EntityManagerInterface $em;
    private FAQMessage $slackMessageTemplate;
    private EmailSenderInterface $emailSender;
    private TranslatorInterface $translator;

    public function __construct(
        EntityManagerInterface $em,
        FAQMessage $message,
        TranslatorInterface $translator,
        EmailSenderInterface $emailSender
    ) {
        $this->em = $em;
        $this->slackMessageTemplate = $message;
        $this->translator = $translator;
        $this->emailSender = $emailSender;
    }

    public function __invoke(AskQuestion $message) : void
    {
        $category = $this->loadCategory($message->getCategoryId());
        $this->storeQuestionInDb($message, $category);
        /** @var Employee[] $employees */
        $employees = $category->getEmployee()->toArray();
        foreach ($employees as $employee) {
            $this->sendNotifications($employee, $message, $category);
        }
    }

    private function storeQuestionInDb(AskQuestion $message, FAQCategory $category) : void
    {
        $question = new FAQAskedQuestion();
        $question
            ->setQuestion($message->getQuestion())
            ->setQuestioner($message->getQuestioner())
            ->setCategory($category)
            ->setLanguage($message->getQuestioner()->getLanguage() ?? 'en')
            ->setCreatedAt()
            ->setUpdatedAt();
        $this->em->persist($question);
    }

    private function sendNotifications(Employee $receiver, AskQuestion $message, FAQCategory $category) : void
    {
        $this->sendEmailNotification($receiver, $message, $category);
        $this->sendSlackNotification($receiver, $message, $category);
    }

    private function sendSlackNotification(Employee $receiver, AskQuestion $message, FAQCategory $category) : void
    {
        $author = $message->getQuestioner();
        $categoryName = $this->getCategoryNameForUser($receiver, $category);
        $question = $message->getQuestion();
        $this->slackMessageTemplate->setData($author, $categoryName, $question)->sendTo($receiver);
    }

    private function sendEmailNotification(Employee $receiver, AskQuestion $message, FAQCategory $category) : void
    {
        $author = $message->getQuestioner();
        $authorName = sprintf("%s %s", $author->getName(), $author->getLastName());
        $language = $receiver->getLanguage();
        $this->translator->setLocale($language);
        $this->emailSender->sendMessage(
            $receiver->getEmail(),
            null,
            sprintf('[Adventure] %s', $this->translator->trans('youReceiveAQuestion')),
            [
                'authorName' => $authorName,
                'category' => $this->getCategoryNameForUser($receiver, $category),
                'question' => $message->getQuestion(),
            ],
            'AdventureBundle:Emails:faq_message.html.twig'
        );
    }

    private function getCategoryNameForUser(Employee $user, FAQCategory $category) : string
    {
        switch ($user->getLanguage()) {
            case 'pl':
                return $category->getNamePl();
            case 'en':
                return $category->getNameEn();
            default:
                return '';
        }
    }

    private function loadCategory(int $id) : FAQCategory
    {
        $repo = $this->em->getRepository(FAQCategory::class);
        /** @var FAQCategory|null $category */
        $category = $repo->find($id);
        if (is_null($category)) {
            throw new NotFoundHttpException("Category with ID $id has not been found");
        }
        return $category;
    }
}
