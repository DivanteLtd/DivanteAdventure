<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\Controller\Api\FAQ;

use Divante\Bundle\AdventureBundle\Entity\FAQ\FAQCategory;
use Divante\Bundle\AdventureBundle\Mappers\FAQ\FAQQuestionsDetailsMapper;
use Divante\Bundle\AdventureBundle\Mappers\FAQ\FAQCategoryDetailsMapper;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Messenger\MessageBusInterface;
use Divante\Bundle\AdventureBundle\Message\FAQ\CreateFAQCategory;
use Divante\Bundle\AdventureBundle\Message\FAQ\CreateFAQQuestion;
use Divante\Bundle\AdventureBundle\Message\FAQ\UpdateFAQQuestion;
use Divante\Bundle\AdventureBundle\Message\FAQ\UpdateFAQCategory;
use Divante\Bundle\AdventureBundle\Message\FAQ\DeleteFAQCategory;
use Divante\Bundle\AdventureBundle\Message\FAQ\DeleteFAQQuestion;

/**
 * FAQ controller.
 *
 * @Route("api/faq/")
 */
class FAQController extends FOSRestController
{
    /**
     * @Route("category", name="get_faq_categories")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     * @param FAQCategoryDetailsMapper $mapper
     * @return View
     */
    public function getAllFAQCategories(FAQCategoryDetailsMapper $mapper) : View
    {
        /** @var FAQCategory[] $categories */
        $categories = $this->getDoctrine()->getRepository(FAQCategory::class)->findAll();
        try {
            return $this->view($mapper->mapEntity($categories), Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("category", name="new_faq_category")
     * @Method("POST")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param Request $request
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function addFAQCategory(Request $request, MessageBusInterface $messageBus): View
    {
        $message = new CreateFAQCategory(
            $request->get('employees'),
            $request->get('namePl'),
            $request->get('nameEn')
        );
        try {
            $messageBus->dispatch($message);
            return $this->view([], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("category/{id}", name="edit_faq_category")
     * @Method("PATCH")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param Request $request
     * @param int $id
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function editFAQCategory(Request $request, MessageBusInterface $messageBus, int $id): View
    {
        $message = new UpdateFAQCategory(
            $id,
            $request->get('employees'),
            $request->get('namePl'),
            $request->get('nameEn')
        );
        try {
            $messageBus->dispatch($message);
            return $this->view([], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("category/{id}", name="delete_faq_category")
     * @Method("DELETE")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param int $id
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function deleteFAQCategory(MessageBusInterface $messageBus, int $id): View
    {
        $message = new DeleteFAQCategory($id);
        try {
            $messageBus->dispatch($message);
            return $this->view([], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("question", name="get_faq_questions")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     * @param FAQQuestionsDetailsMapper $mapper
     * @return View
     */
    public function getAllFAQQuestions(FAQQuestionsDetailsMapper $mapper) : View
    {
        /** @var FAQCategory[] $categories */
        $categories = $this->getDoctrine()->getRepository(FAQCategory::class)->findAll();
        try {
            return $this->view($mapper->mapEntity($categories), Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("question", name="new_faq_question")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function addFAQQuestion(Request $request, MessageBusInterface $messageBus): View
    {
        $employeeId = $this->getUser()->getEmployeeId();
        $message = new CreateFAQQuestion(
            $employeeId,
            $request->get('categoryId'),
            $request->get('questionPl'),
            $request->get('questionEn'),
            $request->get('answerPl'),
            $request->get('answerEn')
        );
        try {
            $messageBus->dispatch($message);
            return $this->view([], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("question/{id}", name="edit_faq_question")
     * @Method("PATCH")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param int $id
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function editFAQQuestion(Request $request, MessageBusInterface $messageBus, int $id): View
    {
        $employeeId = $this->getUser()->getEmployeeId();
        $message = new UpdateFAQQuestion(
            $id,
            $employeeId,
            $request->get('categoryId'),
            $request->get('questionPl'),
            $request->get('questionEn'),
            $request->get('answerPl'),
            $request->get('answerEn')
        );
        try {
            $messageBus->dispatch($message);
            return $this->view([], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("question/{id}", name="delete_faq_question")
     * @Method("DELETE")
     * @Security("has_role('ROLE_USER')")
     * @param int $id
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function deleteFAQQuestion(MessageBusInterface $messageBus, int $id): View
    {
        $employeeId = $this->getUser()->getEmployeeId();
        $message = new DeleteFAQQuestion($id, $employeeId);
        try {
            $messageBus->dispatch($message);
            return $this->view([], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
