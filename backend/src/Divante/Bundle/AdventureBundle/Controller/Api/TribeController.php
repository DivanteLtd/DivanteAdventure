<?php

namespace Divante\Bundle\AdventureBundle\Controller\Api;

use Divante\Bundle\AdventureBundle\Mappers\TribeMapper;
use Divante\Bundle\AdventureBundle\Message\Positions\CreatePositionTribeConnection;
use Divante\Bundle\AdventureBundle\Message\Positions\DeletePositionTribeConnection;
use Divante\Bundle\AdventureBundle\Message\UpdateTribe;
use Divante\Bundle\AdventureBundle\Message\DeleteTribe;
use Divante\Bundle\AdventureBundle\Message\CreateTribe;
use Divante\Bundle\AdventureBundle\Services\Slack\MessageTemplates\IntegrationDisconnectedMessage;
use Divante\Bundle\AdventureBundle\Entity\User;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use \FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\Controller\FOSRestController;
use Divante\Bundle\AdventureBundle\Entity\Tribe;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Tribe controller.
 *
 * @Route("/api/tribe")
 */
class TribeController extends FOSRestController
{
    /**
     * @Route("", name="tribe_index")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     * @param TribeMapper $tribeMapper
     * @return View
     */
    public function indexAction(TribeMapper $tribeMapper) : View
    {
        $repo = $this->getDoctrine()->getRepository(Tribe::class);
        /** @var Tribe[] $tribes */
        $tribes = $repo->findAll();
        $result = array_map($tribeMapper, $tribes);
        return $this->view($result, Response::HTTP_OK);
    }

    /**
     * Add new tribe
     *
     * Access: ROLE_TRIBE_MASTER
     *
     * @Route("", name="tribe_new")
     * @Method("POST")
     * @Security("has_role('ROLE_TRIBE_MASTER')")
     *
     * @param Request $request
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function newAction(Request $request, MessageBusInterface $messageBus): View
    {
        try {
            $name = $request->get('name', '');
            $description = $request->get('description', '');
            $photoUrl = $request->get('photoUrl', '');
            $url = $request->get('url', '');
            $isVirtual = $request->get('isVirtual', false);
            $responsible = $request->get('responsible', []);
            $hrEmail = $request->get('hrEmail', '');
            $freeDayProjectId = $request->get('freeDayProjectId', null);
            $freeDayCategoryId = $request->get('freeDayCategoryId', null);
            $sickLeaveProjectId = $request->get('sickLeaveProjectId', null);
            $sickLeaveCategoryId = $request->get('sickLeaveCategoryId', null);
            $techLeaderId = $request->get('techLeaderId', null);
            $createEntry = new CreateTribe(
                $name,
                $description,
                $photoUrl,
                $url,
                $isVirtual,
                $responsible,
                $hrEmail,
                $freeDayProjectId,
                $freeDayCategoryId,
                $sickLeaveProjectId,
                $sickLeaveCategoryId,
                $techLeaderId,
            );
            $messageBus->dispatch($createEntry);

            return $this->view([], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Edit an existing tribe.
     *
     * Access: ROLE_TRIBE_MASTER
     *
     * @Route("/{id}", name="tribe_edit")
     * @Method("PATCH")
     * @Security("has_role('ROLE_TRIBE_MASTER')")
     *
     * @param int $id
     * @param Request $request
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function editAction(int $id, Request $request, MessageBusInterface $messageBus): View
    {
        $em = $this->getDoctrine()->getManager();
        try {
            /** @var Tribe|null $entry */
            $entry = $em->getRepository(Tribe::class)->find($id);
            $name = $request->get('name', '');
            $description = $request->get('description', '');
            $photoUrl = $request->get('photoUrl', '');
            $url = $request->get('url', '');
            $isVirtual = $request->get('isVirtual', false);
            $responsible = $request->get('responsible', []);
            $hrEmail = $request->get('hrEmail', '');
            $freeDayProjectId = $request->get('freeDayProjectId', null);
            $freeDayCategoryId = $request->get('freeDayCategoryId', null);
            $sickLeaveProjectId = $request->get('sickLeaveProjectId', null);
            $sickLeaveCategoryId = $request->get('sickLeaveCategoryId', null);
            $techLeaderId = $request->get('techLeaderId', null);

            if (is_null($entry)) {
                return $this->view(['error' => "Tribe with ID $id not found"], Response::HTTP_NOT_FOUND);
            }
            $createEntry = new UpdateTribe(
                $entry,
                $name,
                $description,
                $photoUrl,
                $url,
                $isVirtual,
                $responsible,
                $hrEmail,
                $freeDayProjectId,
                $freeDayCategoryId,
                $sickLeaveProjectId,
                $sickLeaveCategoryId,
                $techLeaderId,
            );
            $messageBus->dispatch($createEntry);
            return $this->view([], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Delete tribe
     *
     * @Route("/{id}", name="tribe_delete")
     * @Method("DELETE")
     *
     * Access: ROLE_TRIBE_MASTER
     * @Security("has_role('ROLE_TRIBE_MASTER')")
     *
     * @param int $id
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function deleteAction(int $id, MessageBusInterface $messageBus): View
    {
        $message = new DeleteTribe($id);
        try {
            $messageBus->dispatch($message);
            return $this->view([], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/{tribeId}/position/{positionId}", name="tribe_attach_position")
     * @Method("POST")
     * @Security("has_role('ROLE_TRIBE_MASTER')")
     * @param int $tribeId
     * @param int $positionId
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function attachPositionAction(int $tribeId, int $positionId, MessageBusInterface $messageBus) : View
    {
        $message = new CreatePositionTribeConnection($positionId, $tribeId);
        try {
            $messageBus->dispatch($message);
            return $this->view([]);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/{tribeId}/position/{positionId}", name="tribe_dettach_position")
     * @Method("DELETE")
     * @Security("has_role('ROLE_TRIBE_MASTER')")
     * @param int $tribeId
     * @param int $positionId
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function detachPositionAction(int $tribeId, int $positionId, MessageBusInterface $messageBus) : View
    {
        $message = new DeletePositionTribeConnection($positionId, $tribeId);
        try {
            $messageBus->dispatch($message);
            return $this->view([]);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/{tribeId}/disconnectSlack", name="tribe_disconnect_slack")
     * @Method("POST")
     * @Security("has_role('ROLE_TRIBE_MASTER')")
     * @param int $tribeId
     * @param IntegrationDisconnectedMessage $template
     * @return View
     * @throws Exception
     */
    public function disconnectFromSlack(int $tribeId, IntegrationDisconnectedMessage $template) : View
    {
        /** @var Tribe|null $tribe */
        $tribe = $this->getDoctrine()->getRepository(Tribe::class)->find($tribeId);
        /** @var User $user */
        $user = $this->getUser();
        if (!is_null($tribe)) {
            $template
                ->setResponsible($user->getEmployee())
                ->setReceiver($tribe)
                ->send();
            $tribe->setSlackStatus(Tribe::SLACK_STATUS_NOT_ASKED);
            $this->getDoctrine()->getManager()->flush();
            return $this->view([], Response::HTTP_OK);
        } else {
            return $this->view([], Response::HTTP_NOT_FOUND);
        }
    }
}
