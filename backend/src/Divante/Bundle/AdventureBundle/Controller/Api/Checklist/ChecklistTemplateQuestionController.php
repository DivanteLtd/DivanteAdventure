<?php

namespace Divante\Bundle\AdventureBundle\Controller\Api\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistTemplate;
use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistTemplateQuestion;
use Divante\Bundle\AdventureBundle\Mappers\Checklist\ChecklistTemplateQuestionMapper;
use Divante\Bundle\AdventureBundle\Mappers\Checklist\EditChecklistTemplateQuestionRequestMapper;
use Divante\Bundle\AdventureBundle\Mappers\Checklist\NewChecklistTemplateQuestionRequestMapper;
use Divante\Bundle\AdventureBundle\Message\Checklist\DeleteChecklistTemplateQuestion;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Class ChecklistTemplateQuestionController
 * @package Divante\Bundle\AdventureBundle\Controller\Api\Checklist
 * @Route("api/question/checklist/{templateId}", requirements={"templateId"="\d+"})
 */
class ChecklistTemplateQuestionController extends FOSRestController
{
    /**
     * @Route("", name="checklist_template_question_get")
     * @Method("GET")
     * @Security("has_role('ROLE_HELPDESK') or has_role('ROLE_HR')")
     * @param int $templateId
     * @param EntityManagerInterface $em
     * @param ChecklistTemplateQuestionMapper $mapper
     * @return View
     */
    public function getAllQuestions(
        int $templateId,
        EntityManagerInterface $em,
        ChecklistTemplateQuestionMapper $mapper
    ) : View {
        $templateRepo = $em->getRepository(ChecklistTemplate::class);
        /** @var ChecklistTemplate|null $template */
        $template = $templateRepo->find($templateId);
        if (is_null($template)) {
            return $this->view([], Response::HTTP_NOT_FOUND);
        }
        $questionsRepo = $em->getRepository(ChecklistTemplateQuestion::class);
        /** @var ChecklistTemplateQuestion[] $questions */
        $questions = $questionsRepo->findBy([
            'checklist' => $template,
        ]);
        $view = array_map($mapper, $questions);
        return $this->view($view, Response::HTTP_OK);
    }

    /**
     * @Route("", name="checklist_template_question_add")
     * @Method("POST")
     * @Security("has_role('ROLE_HELPDESK') or has_role('ROLE_HR')")
     * @param int $templateId
     * @param Request $request
     * @param NewChecklistTemplateQuestionRequestMapper $requestMapper
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function addNewQuestion(
        int $templateId,
        Request $request,
        NewChecklistTemplateQuestionRequestMapper $requestMapper,
        MessageBusInterface $messageBus
    ) : View {
        $message = $requestMapper->mapToMessage($request, $templateId);
        $messageBus->dispatch($message);
        $this->getDoctrine()->getManager()->flush();
        return $this->view([], Response::HTTP_OK);
    }

    /**
     * @Route("/{questionId}", name="checklist_template_question_edit", requirements={"questionId"="\d+"})
     * @Method("PATCH")
     * @Security("has_role('ROLE_HELPDESK') or has_role('ROLE_HR')")
     * @param Request $request
     * @param int $templateId
     * @param int $questionId
     * @param EditChecklistTemplateQuestionRequestMapper $requestMapper
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function editQuestion(
        Request $request,
        int $templateId,
        int $questionId,
        EditChecklistTemplateQuestionRequestMapper $requestMapper,
        MessageBusInterface $messageBus
    ) : View {
        $message = $requestMapper->mapToMessage($request, $templateId, $questionId);
        $messageBus->dispatch($message);
        $this->getDoctrine()->getManager()->flush();
        return $this->view([], Response::HTTP_OK);
    }

    /**
     * @Route("/{questionId}", name="checklist_template_question_delete", requirements={"questionId"="\d+"})
     * @Method("DELETE")
     * @Security("has_role('ROLE_HELPDESK') or has_role('ROLE_HR')")
     * @param int $templateId
     * @param int $questionId
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function deleteQuestion(int $templateId, int $questionId, MessageBusInterface $messageBus) : View
    {
        $message = new DeleteChecklistTemplateQuestion($templateId, $questionId);
        $messageBus->dispatch($message);
        $this->getDoctrine()->getManager()->flush();
        return $this->view([], Response::HTTP_OK);
    }
}
