<?php

namespace Divante\Bundle\AdventureBundle\Controller\Api;

use Divante\Bundle\AdventureBundle\Mappers\NewsMapper;
use Divante\Bundle\AdventureBundle\Message\Dashboard\CreatePost;
use Divante\Bundle\AdventureBundle\Message\Dashboard\DeletePost;
use Divante\Bundle\AdventureBundle\Message\Dashboard\EditPost;
use Divante\Bundle\AdventureBundle\Entity\User;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\Controller\FOSRestController;
use Divante\Bundle\AdventureBundle\Entity\News;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @Route("api/news")
 */
class NewsController extends FOSRestController
{
    private NewsMapper $newsMapper;

    public function __construct(NewsMapper $newsMapper)
    {
        $this->newsMapper = $newsMapper;
    }


    /**
     * @Route("", name="news_index")
     * @Method("GET")
     *
     * @Security("has_role('ROLE_USER')")
     * @return View
     */
    public function indexAction() : View
    {
        return $this->view($this->getAllPosts(), Response::HTTP_OK);
    }

    /**
     * @Route("", name="news_add")
     * @Method("POST")
     *
     * @Security("has_role('ROLE_HR')")
     * @param Request $request
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function addAction(Request $request, MessageBusInterface $messageBus): View
    {
        $message = new CreatePost(
            $request->get('title'),
            $request->get('banner'),
            $request->get('desc'),
            $request->get('type'),
            $this->getUser()->getEmployeeId()
        );
        $messageBus->dispatch($message);
        return $this->view(
            $this->getAllPosts(),
            Response::HTTP_OK
        );
    }

    /**
     * @Route("/{id}", name="news_delete")
     * @Method("DELETE")
     *
     * @Security("has_role('ROLE_HR')")
     * @param int $id
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function deleteAction(int $id, MessageBusInterface $messageBus): View
    {
        /** @var User $user */
        $user = $this->getUser();
        $employeeId = $user->getEmployeeId() ?? -1;
        $messageBus->dispatch(new DeletePost($id, $employeeId));
        return $this->view($this->getAllPosts(), Response::HTTP_OK);
    }


    /**
     * @Route("/{id}", name="news_update")
     * @Method("PATCH")
     *
     * @Security("has_role('ROLE_HR')")
     * @param int $id
     * @param Request $request
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function editAction(int $id, Request $request, MessageBusInterface $messageBus): View
    {
        $message = new EditPost(
            $id,
            $request->get('title'),
            $request->get('banner'),
            $request->get('desc'),
            $request->get('type')
        );
        $messageBus->dispatch($message);
        return $this->view($this->getAllPosts(), Response::HTTP_OK);
    }

    /**
     * @Route("/{id}", name="news_html_show")
     * @Method("GET")
     *
     * @Security("has_role('ROLE_USER')")
     * @param int $id
     * @return View
     */
    public function getHtmlNews(int $id) : View
    {

        /** @var News|null $news */
        $news = $this->getDoctrine()->getRepository(News::class)->find($id);
        if (!is_null($news) && $news->getType() === News::TYPE_HTML) {
            return $this->view($news->getDescription(), Response::HTTP_OK);
        } else {
            return $this->view("404 Not Found", Response::HTTP_NOT_FOUND);
        }
    }
    /** @return array<int,array<string,mixed>> */
    protected function getAllPosts() :array
    {
        /** @var News[] $news */
        $news = $this->getDoctrine()->getRepository(News::class)->findAll();
        return array_map($this->newsMapper, $news);
    }
}
