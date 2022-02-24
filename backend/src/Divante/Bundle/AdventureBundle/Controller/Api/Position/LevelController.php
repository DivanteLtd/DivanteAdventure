<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\Controller\Api\Position;

use Divante\Bundle\AdventureBundle\Entity\Level;
use Divante\Bundle\AdventureBundle\Mappers\LevelMapper;
use Divante\Bundle\AdventureBundle\Message\Positions\CreateLevel;
use Divante\Bundle\AdventureBundle\Message\Positions\DeleteLevel;
use Divante\Bundle\AdventureBundle\Message\Positions\UpdateLevel;
use Doctrine\Common\Collections\Criteria;
use Exception;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Class LevelController
 * @package Divante\Bundle\AdventureBundle\Controller\Api\Position
 * @Route("/api/level")
 */
class LevelController extends FOSRestController
{
    /**
     * @Route("", name="level_get")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     * @param LevelMapper $levelMapper
     * @return View
     */
    public function indexAction(LevelMapper $levelMapper) : View
    {
        $repo = $this->getDoctrine()->getRepository(Level::class);
        /** @var Level[] $levels */
        $levels = $repo->findAll();
        $result = array_map(
            function (Level $level) use ($levelMapper) {
                return $levelMapper->mapEntity($level);
            },
            $levels
        );
        return $this->view($result);
    }

    /**
     * @Route("", name="level_post")
     * @Method("POST")
     * @Security("has_role('ROLE_TRIBE_MASTER')")
     * @param Request $request
     * @param MessageBusInterface $messageBus
     * @param LevelMapper $levelMapper
     * @return View
     */
    public function createAction(Request $request, MessageBusInterface $messageBus, LevelMapper $levelMapper) : View
    {
        $name = $request->get('name');
        $strategyId = $request->get('strategyId');
        $priority = $request->get('priority', 0);
        if (is_null($name)) {
            return $this->view(['error' => 'Lacking parameter "name"'], Response::HTTP_BAD_REQUEST);
        }
        if (is_null($strategyId)) {
            return $this->view(['error' => 'Lacking parameter "strategyId"'], Response::HTTP_BAD_REQUEST);
        }
        $message = new CreateLevel($name, $strategyId, $priority);
        try {
            $messageBus->dispatch($message);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        $repo = $this->getDoctrine()->getRepository(Level::class);
        /** @var Level $created */
        $created = $repo->findBy([
            'name' => $name
        ], [
            'createdAt' => Criteria::DESC
        ])[0];
        return $this->view([$levelMapper->mapEntity($created)]);
    }

    /**
     * @Route("/{levelId}", name="level_patch")
     * @Method("PATCH")
     * @Security("has_role('ROLE_TRIBE_MASTER')")
     * @param Request $request
     * @param int $levelId
     * @param MessageBusInterface $messageBus
     * @param LevelMapper $levelMapper
     * @return View
     */
    public function updateAction(
        Request $request,
        int $levelId,
        MessageBusInterface $messageBus,
        LevelMapper $levelMapper
    ) : View {
        $name = $request->get('name');
        $strategyId = $request->get('strategyId');
        $priority = $request->get('priority', 0);
        $message = new UpdateLevel($levelId, $name, $strategyId, $priority);
        try {
            $messageBus->dispatch($message);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
        $repo = $this->getDoctrine()->getRepository(Level::class);
        /** @var Level|null $level */
        $level = $repo->find($levelId);
        if (is_null($level)) {
            return $this->view(['error' => 'Level not found'], Response::HTTP_NOT_FOUND);
        }
        return $this->view($levelMapper->mapEntity($level));
    }

    /**
     * @Route("/{levelId}", name="level_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_TRIBE_MASTER')")
     * @param int $levelId
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function deleteAction(int $levelId, MessageBusInterface $messageBus) : View
    {
        $message = new DeleteLevel($levelId);
        try {
            $messageBus->dispatch($message);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
        return $this->view([]);
    }
}
