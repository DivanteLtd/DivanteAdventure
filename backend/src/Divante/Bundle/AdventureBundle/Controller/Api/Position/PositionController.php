<?php

namespace Divante\Bundle\AdventureBundle\Controller\Api\Position;

use Divante\Bundle\AdventureBundle\Entity\Position;
use Divante\Bundle\AdventureBundle\Mappers\PositionMapper;
use Divante\Bundle\AdventureBundle\Message\Positions\CreatePosition;
use Divante\Bundle\AdventureBundle\Message\Positions\DeletePosition;
use Divante\Bundle\AdventureBundle\Message\Positions\UpdatePosition;
use Doctrine\Common\Collections\Criteria;
use Exception;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Class PositionController
 * @package Divante\Bundle\AdventureBundle\Controller\Api\Position
 * @Route("/api/position")
 */
class PositionController extends FOSRestController
{
    /** @var MessageBusInterface */
    private $messageBus;
    /** @var PositionMapper */
    private $positionMapper;

    public function __construct(MessageBusInterface $messageBus, PositionMapper $positionMapper)
    {
        $this->messageBus = $messageBus;
        $this->positionMapper = $positionMapper;
    }

    /**
     * @Route("", name="position_get")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     * @return View
     */
    public function indexAction() : View
    {
        $repo = $this->getDoctrine()->getRepository(Position::class);
        /** @var Position[] $positions */
        $positions = $repo->findAll();
        $result = array_map(
            function (Position $position) {
                return $this->positionMapper->mapEntity($position);
            },
            $positions
        );
        return $this->view($result);
    }

    /**
     * @Route("", name="position_post")
     * @Method("POST")
     * @Security("has_role('ROLE_TRIBE_MASTER')")
     * @param Request $request
     * @return View
     */
    public function createAction(Request $request) : View
    {
        $name = $request->get('name');
        $strategyId = $request->get('strategyId');
        if (is_null($name)) {
            return $this->view(['error' => 'Lacking parameter "name"'], Response::HTTP_BAD_REQUEST);
        }
        if (is_null($strategyId)) {
            return $this->view(['error' => 'Lacking parameter "strategyId"'], Response::HTTP_BAD_REQUEST);
        }
        $message = new CreatePosition($name, $strategyId);
        try {
            $this->messageBus->dispatch($message);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
        $repo = $this->getDoctrine()->getRepository(Position::class);
        /** @var Position $created */
        $created = $repo->findBy([
            'name' => $name
        ], [
            'createdAt' => Criteria::DESC
        ])[0];
        return $this->view($this->positionMapper->mapEntity($created));
    }

    /**
     * @Route("/{positionId}", name="position_patch")
     * @Method("PATCH")
     * @Security("has_role('ROLE_TRIBE_MASTER')")
     * @param Request $request
     * @param int $positionId
     * @return View
     */
    public function updateAction(Request $request, int $positionId) : View
    {
        $name = $request->get('name');
        $strategyId = $request->get('strategyId');
        $message = new UpdatePosition($positionId, $strategyId, $name);
        try {
            $this->messageBus->dispatch($message);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
        $repo = $this->getDoctrine()->getRepository(Position::class);
        /** @var Position|null $position */
        $position = $repo->find($positionId);
        if (is_null($position)) {
            return $this->view(['error' => 'Position not found'], Response::HTTP_NOT_FOUND);
        }
        return $this->view($this->positionMapper->mapEntity($position));
    }

    /**
     * @Route("/{positionId}", name="position_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_TRIBE_MASTER')")
     * @param int $positionId
     * @return View
     */
    public function deleteAction(int $positionId) : View
    {
        $message = new DeletePosition($positionId);
        try {
            $this->messageBus->dispatch($message);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
        return $this->view([]);
    }
}
