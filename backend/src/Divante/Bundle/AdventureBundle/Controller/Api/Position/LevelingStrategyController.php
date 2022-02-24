<?php

namespace Divante\Bundle\AdventureBundle\Controller\Api\Position;

use Divante\Bundle\AdventureBundle\Entity\LevelingStrategy;
use Divante\Bundle\AdventureBundle\Mappers\LevelingStrategyMapper;
use Divante\Bundle\AdventureBundle\Message\Positions\CreateLevelingStrategy;
use Divante\Bundle\AdventureBundle\Message\Positions\DeleteLevelingStrategy;
use Divante\Bundle\AdventureBundle\Message\Positions\UpdateLevelingStrategy;
use Doctrine\Common\Collections\Criteria;
use Exception;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Class LevelingStrategyController
 * @package Divante\Bundle\AdventureBundle\Controller\Api\Position
 * @Route("/api/strategy")
 */
class LevelingStrategyController extends FOSRestController
{
    /** @var MessageBusInterface */
    private $messageBus;
    /** @var LevelingStrategyMapper */
    private $strategyMapper;

    public function __construct(MessageBusInterface $messageBus, LevelingStrategyMapper $strategyMapper)
    {
        $this->messageBus = $messageBus;
        $this->strategyMapper = $strategyMapper;
    }

    /**
     * @Route("", name="strategy_get")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     * @return View
     */
    public function indexAction() : View
    {
        $repo = $this->getDoctrine()->getRepository(LevelingStrategy::class);
        /** @var LevelingStrategy[] $strategies */
        $strategies = $repo->findAll();
        $result = array_map(
            function (LevelingStrategy $strategy) {
                return $this->strategyMapper->mapEntity($strategy);
            },
            $strategies
        );
        return $this->view($result);
    }

    /**
     * @Route("", name="strategy_post")
     * @Method("POST")
     * @Security("has_role('ROLE_TRIBE_MASTER')")
     * @param Request $request
     * @return View
     */
    public function createAction(Request $request) : View
    {
        $name = $request->get('name');
        if (is_null($name)) {
            return $this->view(['error' => 'Lacking parameter "name"'], Response::HTTP_BAD_REQUEST);
        }
        $message = new CreateLevelingStrategy($name);
        try {
            $this->messageBus->dispatch($message);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
        $repo = $this->getDoctrine()->getRepository(LevelingStrategy::class);
        /** @var LevelingStrategy $strategy */
        $strategy = $repo->findBy([
            'name' => $name
        ], [
            'createdAt' => Criteria::DESC
        ])[0];
        return $this->view($this->strategyMapper->mapEntity($strategy));
    }

    /**
     * @Route("/{strategyId}", name="strategy_patch")
     * @Method("PATCH")
     * @Security("has_role('ROLE_TRIBE_MASTER')")
     * @param int $strategyId
     * @param Request $request
     * @return View
     */
    public function updateAction(int $strategyId, Request $request) : View
    {
        $name = $request->get('name');
        $message = new UpdateLevelingStrategy($strategyId, $name);
        try {
            $this->messageBus->dispatch($message);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
        $repo = $this->getDoctrine()->getRepository(LevelingStrategy::class);
        /** @var LevelingStrategy|null $strategy */
        $strategy = $repo->find($strategyId);
        if (is_null($strategy)) {
            return $this->view(['error' => 'Strategy not found'], Response::HTTP_NOT_FOUND);
        }
        return $this->view($this->strategyMapper->mapEntity($strategy));
    }

    /**
     * @Route("/{strategyId}", name="strategy_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_TRIBE_MASTER')")
     * @param int $strategyId
     * @return View
     */
    public function deleteAction(int $strategyId) : View
    {
        $message = new DeleteLevelingStrategy($strategyId);
        try {
            $this->messageBus->dispatch($message);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
        return $this->view([]);
    }
}
