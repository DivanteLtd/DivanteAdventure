<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\Controller\Api\FAQ;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\FAQ\FAQAskedQuestion;
use Divante\Bundle\AdventureBundle\Entity\FAQ\FAQCategory;
use Divante\Bundle\AdventureBundle\Mappers\FAQ\AskedQuestionMapper;
use Divante\Bundle\AdventureBundle\Message\FAQ\AnswerQuestion;
use Divante\Bundle\AdventureBundle\Message\FAQ\AskQuestion;
use Divante\Bundle\AdventureBundle\Message\FAQ\RejectQuestion;
use Divante\Bundle\AdventureBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Employee controller.
 *
 * @Route("api/faq/asked")
 */
class FAQMessageController extends FOSRestController
{
    /**
     * @Route("", name="faq_asked_create")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function sendFAQMessage(Request $request, MessageBusInterface $messageBus): View
    {
        try {
            /** @var Employee $author */
            $author = $this->getUser()->getEmployee();
            $question = $request->get('question', '');
            $categoryId = (int)$request->get('categoryId', -1);
            $message = new AskQuestion($author, $question, $categoryId);
            $messageBus->dispatch($message);
            $this->getDoctrine()->getManager()->flush();
            return $this->view([], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("", name="faq_asked_get_all")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     * @param EntityManagerInterface $em
     * @param AskedQuestionMapper $mapper
     * @return View
     */
    public function getAvailableQuestions(EntityManagerInterface $em, AskedQuestionMapper $mapper) : View
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var Employee $person */
        $person = $user->getEmployee();
        /** @var FAQCategory[] $categories */
        $categories = $em->getRepository(FAQCategory::class)->findAll();
        if (!in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            $categories = array_filter(
                $categories,
                fn(FAQCategory $cat) => $cat->getEmployee()->contains($person),
            );
        }

        /** @var FAQAskedQuestion[] $questions */
        $questions = $em->getRepository(FAQAskedQuestion::class)->findBy([
            'category' => $categories
        ]);
        $json = array_map(fn(FAQAskedQuestion $q) => $mapper($q), $questions);
        return $this->view($json, Response::HTTP_OK);
    }

    /**
     * @Route("/{questionId}/confirm", name="faq_asked_confirm_question")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     * @param int $questionId
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function confirmQuestion(int $questionId, MessageBusInterface $messageBus) : View
    {
        /** @var User $user */
        $user = $this->getUser();
        $employee = $user->getEmployee();
        $message = new AnswerQuestion($questionId, $employee);
        $messageBus->dispatch($message);
        return $this->view([], Response::HTTP_OK);
    }

    /**
     * @Route("/{questionId}/reject", name="faq_asked_reject_question")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     * @param int $questionId
     * @param Request $request
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function rejectQuestion(int $questionId, Request $request, MessageBusInterface $messageBus) : View
    {
        /** @var User $user */
        $user = $this->getUser();
        $employee = $user->getEmployee();
        $reason = $request->get('reason', '');
        $message = new RejectQuestion($questionId, $employee, $reason);
        $messageBus->dispatch($message);
        return $this->view([], Response::HTTP_OK);
    }
}
