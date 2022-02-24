<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 13.02.19
 * Time: 13:34
 */

namespace Divante\Bundle\AdventureBundle\Controller\Api;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\LeavePeriod;
use Divante\Bundle\AdventureBundle\Entity\LeaveRequest;
use Divante\Bundle\AdventureBundle\Entity\Notification;
use Divante\Bundle\AdventureBundle\Mappers\LeaveRequestMapper;
use Divante\Bundle\AdventureBundle\Message\Dashboard\CreateNotification;
use Divante\Bundle\AdventureBundle\Message\Leaves\CreateRequest;
use Divante\Bundle\AdventureBundle\Message\Leaves\DeleteRequest;
use Divante\Bundle\AdventureBundle\Message\Leaves\UpdateRequest;
use Divante\Bundle\AdventureBundle\Message\Leaves\UpdateRequestDay;
use Divante\Bundle\AdventureBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Class LeaveRequestController
 * @package Divante\Bundle\AdventureBundle\Controller\Api
 * @Route("/api/leaveRequest")
 */
class LeaveRequestController extends FOSRestController
{
    /** @var MessageBusInterface */
    private $messageBus;
    /** @var LeaveRequestMapper */
    private $leaveRequestMapper;

    public function __construct(MessageBusInterface $messageBus, LeaveRequestMapper $leaveRequestMapper)
    {
        $this->messageBus = $messageBus;
        $this->leaveRequestMapper = $leaveRequestMapper;
    }

    /**
     * @Route("/{periodId}", name="request_create")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     * @param int $periodId
     * @param Request $request
     * @return View
     */
    public function createNewRequest(int $periodId, Request $request) : View
    {
        $description = $request->get('status') === 0
            ? Notification::MANAGER_NEW_REQUEST : Notification::MANAGER_NEW_PLANNED_REQUEST;
        $path = Notification::REQUESTS_PATH;
        $managerId = $request->get('managerId');
        $subject = $request->get('subject');
        /** @var User $user */
        $user = $this->getUser();
        $employeeId = $user->getEmployeeId();
        try {
            $createEntry = new CreateNotification($managerId, $description, $subject, $path);
            $message = new CreateRequest(
                $periodId,
                $managerId,
                $request->get('days'),
                $request->get('attachments', []),
                $request->get('comment', null),
                $request->get('status', 0),
                $this->getDoctrine()->getManager(),
                $employeeId
            );
            $this->messageBus->dispatch($message);
            $this->messageBus->dispatch($createEntry);
        } catch (\Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
        return $this->view([], Response::HTTP_OK);
    }

    /**
     * @Route("", name="request_get")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     * @param EntityManagerInterface $em
     * @return View
     */
    public function getCreatedRequestsByUser(EntityManagerInterface $em) : View
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var Employee $employee */
        $employee = $em->getRepository(Employee::class)->find($user->getEmployeeId());
        return $this->getCreatedRequestsFromDatabase($employee, $em);
    }

    /**
     * @Route("/{employeeId<\d+>}", name="request_get_by_employee")
     * @Method("GET")
     * @Security("has_role('ROLE_MANAGER')")
     * @param int $employeeId
     * @param EntityManagerInterface $em
     * @return View
     */
    public function getCreatedRequestsByEmployee(int $employeeId, EntityManagerInterface $em) : View
    {
        /** @var Employee|null $employee */
        $employee = $em->getRepository(Employee::class)->find($employeeId);
        if (is_null($employee)) {
            return $this->view([], Response::HTTP_NOT_FOUND);
        }
        return $this->getCreatedRequestsFromDatabase($employee, $em);
    }

    /**
     * @Route("/acceptable", name="request_get_acceptable")
     * @Method("GET")
     * @Security("has_role('ROLE_MANAGER')")
     * @param EntityManagerInterface $em
     * @return View
     */
    public function getAcceptableRequestsByUser(EntityManagerInterface $em) : View
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var Employee $employee */
        $employee = $em->getRepository(Employee::class)->find($user->getEmployeeId());
        /** @var LeaveRequest[] $requests */
        $requests = $em->getRepository(LeaveRequest::class)->findBy(['manager' => $employee]);
        $result = [];
        foreach ($requests as $request) {
            $result[] = $this->leaveRequestMapper->mapEntity($request);
        }
        return $this->view($result, Response::HTTP_OK);
    }

    private function getCreatedRequestsFromDatabase(Employee $employee, EntityManagerInterface $em) : View
    {
        $periods = $em->getRepository(LeavePeriod::class)->findBy(['employee' => $employee]);
        /** @var LeaveRequest[] $requests */
        $requests = $em->getRepository(LeaveRequest::class)->findBy(['period' => $periods]);
        $result = [];
        foreach ($requests as $request) {
            $result[] = $this->leaveRequestMapper->mapEntity($request);
        }
        return $this->view($result, Response::HTTP_OK);
    }

    /**
     * @Route("/{requestId}", name="request_update")
     * @Method("PATCH")
     * @Security("has_role('ROLE_USER')")
     * @param int $requestId
     * @param Request $request
     * @return View
     */
    public function updateRequest(int $requestId, Request $request) : View
    {
        /** @var User $user */
        $user = $this->getUser();
        try {
            $days = $request->get('days', null);
            $message = new UpdateRequest(
                $requestId,
                $days,
                $request->get('status'),
                $request->get('comment'),
                $user
            );
            $this->messageBus->dispatch($message);
            return $this->view([], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/day/{dayId}", name="request_day_update")
     * @Method("PATCH")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param int $dayId
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return View
     */
    public function updateDay(int $dayId, EntityManagerInterface $em, Request $request) : View
    {
        try {
            $message = new UpdateRequestDay($dayId, $request->get('status'), $request->get('type'), $em);
            $this->messageBus->dispatch($message);
            return $this->view([], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/{requestId}", name="request_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param int $requestId
     * @param EntityManagerInterface $em
     * @return View
     */
    public function deleteRequest(int $requestId, EntityManagerInterface $em) : View
    {
        try {
            $message = new DeleteRequest($requestId, $em);
            $this->messageBus->dispatch($message);
            return $this->view([], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
