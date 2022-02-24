<?php

namespace Divante\Bundle\AdventureBundle\Controller\Api;

use Divante\Bundle\AdventureBundle\Entity\Link;
use Divante\Bundle\AdventureBundle\Mappers\LinkMapper;
use Divante\Bundle\AdventureBundle\Message\Dashboard\CreateLink;
use Divante\Bundle\AdventureBundle\Message\Dashboard\DeleteLink;
use Divante\Bundle\AdventureBundle\Message\Dashboard\UpdateLink;
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
 * @package Divante\Bundle\AdventureBundle\Controller\Api
 * @Route("/api/links")
 */
class LinkController extends FOSRestController
{
    /** @var MessageBusInterface */
    private $messageBus;
    /** @var LinkMapper */
    private $linkMapper;

    public function __construct(MessageBusInterface $messageBus, LinkMapper $linkMapper)
    {
        $this->messageBus = $messageBus;
        $this->linkMapper = $linkMapper;
    }

    /**
     * @Route("", name="links_get")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     * @return View
     */
    public function indexAction() : View
    {
        $repo = $this->getDoctrine()->getRepository(Link::class);
        $links = $repo->findAll();
        $result = array_map(
            function (Link $link) {
                return $this->linkMapper->mapEntity($link);
            },
            $links
        );
        return $this->view($result);
    }

    /**
     * @Route("", name="link_post")
     * @Method("POST")
     * @Security("has_role('ROLE_TRIBE_MASTER')")
     * @param Request $request
     * @return View
     */
    public function createAction(Request $request) : View
    {
        $title = $request->get('title');
        $url = $request->get('url');
        $userId = $this->getUser()->getEmployeeId();
        if (is_null($title)) {
            return $this->view(['error' => 'Lack of "title" parameter'], Response::HTTP_BAD_REQUEST);
        }
        if (is_null($url)) {
            return $this->view(['error' => 'Lack of "url" parameter'], Response::HTTP_BAD_REQUEST);
        }
        if (is_null($userId)) {
            return $this->view(['error' => 'Lack of "userId" parameter'], Response::HTTP_BAD_REQUEST);
        }
        $message = new CreateLink($title, $url, $userId);
        try {
            $this->messageBus->dispatch($message);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return $this->view([], Response::HTTP_OK);
    }

    /**
     * @Route("/{linkId}", name="link_patch")
     * @Method("PATCH")
     * @Security("has_role('ROLE_TRIBE_MASTER')")
     * @param Request $request
     * @param int $linkId
     * @return View
     */
    public function updateAction(Request $request, int $linkId) : View
    {
        $title = $request->get('title');
        $url = $request->get('url');
        $userId = $this->getUser()->getEmployeeId();
        $message = new UpdateLink($linkId, $title, $url, $userId);
        try {
            $this->messageBus->dispatch($message);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return $this->view([], Response::HTTP_OK);
    }

    /**
     * @Route("/{levelId}", name="link_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_TRIBE_MASTER')")
     * @param int $levelId
     * @return View
     */
    public function deleteAction(int $levelId) : View
    {
        $message = new DeleteLink($levelId);
        try {
            $this->messageBus->dispatch($message);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
        return $this->view([], Response::HTTP_OK);
    }
}
