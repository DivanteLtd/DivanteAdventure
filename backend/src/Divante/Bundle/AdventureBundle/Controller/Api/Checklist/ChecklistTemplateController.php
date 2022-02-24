<?php

namespace Divante\Bundle\AdventureBundle\Controller\Api\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistTemplate;
use Divante\Bundle\AdventureBundle\Mappers\Checklist\ApplyChecklistTemplateRequestMapper;
use Divante\Bundle\AdventureBundle\Mappers\Checklist\ChecklistTemplateMapper;
use Divante\Bundle\AdventureBundle\Mappers\Checklist\EditChecklistTemplateRequestMapper;
use Divante\Bundle\AdventureBundle\Mappers\Checklist\NewChecklistTemplateRequestMapper;
use Divante\Bundle\AdventureBundle\Message\Checklist\DeleteChecklistTemplate;
use Divante\Bundle\AdventureBundle\Entity\User;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Class ChecklistTemplateController
 * @package Divante\Bundle\AdventureBundle\Controller\Api\Checklist
 * @Route("api/template/checklist")
 */
class ChecklistTemplateController extends FOSRestController
{
    /**
     * @Route("", name="checklist_template_list")
     * @Method("GET")
     * @Security("has_role('ROLE_HELPDESK') or has_role('ROLE_HR')")
     * @param ChecklistTemplateMapper $mapper
     * @return View
     */
    public function getTemplates(ChecklistTemplateMapper $mapper) : View
    {
        $repo = $this->getDoctrine()->getRepository(ChecklistTemplate::class);
        $templates = $repo->findAll();
        $result = array_map($mapper, $templates);
        return $this->view($result, Response::HTTP_OK);
    }

    /**
     * @Route("", name="checklist_template_create")
     * @Method("POST")
     * @Security("has_role('ROLE_HELPDESK') or has_role('ROLE_HR')")
     * @param Request $request
     * @param NewChecklistTemplateRequestMapper $requestMapper
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function createNewTemplate(
        Request $request,
        NewChecklistTemplateRequestMapper $requestMapper,
        MessageBusInterface $messageBus
    ) : View {
        $message = $requestMapper->mapToMessage($request);
        $messageBus->dispatch($message);
        $this->getDoctrine()->getManager()->flush();
        return $this->view([], Response::HTTP_OK);
    }

    /**
     * @Route("/{id}", name="checklist_template_edit", requirements={"id"="\d+"})
     * @Method("PATCH")
     * @Security("has_role('ROLE_HELPDESK') or has_role('ROLE_HR')")
     * @param int $id
     * @param Request $request
     * @param EditChecklistTemplateRequestMapper $requestMapper
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function editTemplate(
        int $id,
        Request $request,
        EditChecklistTemplateRequestMapper $requestMapper,
        MessageBusInterface $messageBus
    ) : View {
        $message = $requestMapper->mapToMessage($request, $id);
        $messageBus->dispatch($message);
        $this->getDoctrine()->getManager()->flush();
        return $this->view([], Response::HTTP_OK);
    }

    /**
     * @Route("/{id}", name="checklist_template_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     * @Security("has_role('ROLE_HELPDESK') or has_role('ROLE_HR')")
     * @param int $id
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function deleteTemplate(int $id, MessageBusInterface $messageBus) : View
    {
        $message = new DeleteChecklistTemplate($id);
        $messageBus->dispatch($message);
        $this->getDoctrine()->getManager()->flush();
        return $this->view([], Response::HTTP_OK);
    }

    /**
     * @Route("/{id}/apply", name="checklist_template_apply", requirements={"id"="\d+"})
     * @Method("POST")
     * @Security("has_role('ROLE_HELPDESK') or has_role('ROLE_HR')")
     * @param int $id
     * @param Request $request
     * @param ApplyChecklistTemplateRequestMapper $requestMapper
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function applyTemplate(
        int $id,
        Request $request,
        ApplyChecklistTemplateRequestMapper $requestMapper,
        MessageBusInterface $messageBus
    ) : View {
        /** @var User $user */
        $user = $this->getUser();
        $message = $requestMapper->mapToMessage($request, $id, $user->getEmployeeId() ?? -1);
        $messageBus->dispatch($message);
        $this->getDoctrine()->getManager()->flush();
        return $this->view([], Response::HTTP_OK);
    }
}
