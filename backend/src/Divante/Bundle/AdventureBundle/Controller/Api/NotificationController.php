<?php

namespace Divante\Bundle\AdventureBundle\Controller\Api;

use Divante\Bundle\AdventureBundle\Entity\Notification;
use Divante\Bundle\AdventureBundle\Infrastructure\Mercure\MercureDecorator;
use Divante\Bundle\AdventureBundle\Mappers\NotificationMapper;
use Divante\Bundle\AdventureBundle\Message\Dashboard\DeleteNotification;
use Divante\Bundle\AdventureBundle\Message\Dashboard\UpdateNotification;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Notification controller.
 *
 * @Route("api/notification")
 */
class NotificationController extends FOSRestController
{
    /** @var MessageBusInterface */
    private $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    /**
     * Lists notification per user.
     *
     * @Route("", name="notification_get")
     * @Method("GET")
     *
     * @Security("has_role('ROLE_USER')")
     * @param NotificationMapper $mapper
     * @param MercureDecorator $decorator
     * @return View
     */
    public function getNotifications(NotificationMapper $mapper, MercureDecorator $decorator): View
    {
        try {
            $employeeId = $this->getUser()->getEmployeeId();
            $em = $this->getDoctrine()->getManager();
            /** @var Notification[] $notifications */
            $notifications = $em->getRepository(Notification::class)->findBy(['employeeId' => $employeeId]);
            /** @var array<string,mixed> $result */
            $result = array_map($mapper, $notifications);
            $decoratedResult = $decorator->decorate($result, "/notifications/$employeeId");
            return $this->view($decoratedResult, Response::HTTP_OK);
        } catch (\Exception $exception) {
            return $this->view($exception, Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Unmark notification (set bold to false)
     *
     * @Security("has_role('ROLE_USER')")
     * @Route("/{id}", name="notification_update")
     * @Method("POST")
     * @param int $id
     * @return View
     */
    public function unmark(int $id): View
    {
        $message = new UpdateNotification($id);
        try {
            $this->messageBus->dispatch($message);
            return $this->view([], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Delete a notification.
     *
     * @Security("has_role('ROLE_USER')")
     *
     * @Route("/{id}", name="notification_delete")
     * @Method("DELETE")
     *
     * @param int $id
     * @return View
     */
    public function deleteAction(int $id): View
    {
        $message = new DeleteNotification($id);
        try {
            $this->messageBus->dispatch($message);
            return $this->view([], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
