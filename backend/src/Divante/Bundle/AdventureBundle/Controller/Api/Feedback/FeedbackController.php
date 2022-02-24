<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\Controller\Api\Feedback;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Tribe;
use Divante\Bundle\AdventureBundle\Entity\Feedback\Feedback;
use Divante\Bundle\AdventureBundle\Mappers\Feedback\LeaderStructureMapper;
use Divante\Bundle\AdventureBundle\Mappers\Feedback\FeedbackMapper;
use Divante\Bundle\AdventureBundle\Message\Feedback\CreateFeedback;
use Divante\Bundle\AdventureBundle\Message\Feedback\UpdateFeedback;
use Divante\Bundle\AdventureBundle\Message\Feedback\DeleteFeedback;
use Exception;
use Divante\Bundle\AdventureBundle\Entity\User;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Feedback controller.
 *
 * @Route("api/feedback")
 */
class FeedbackController extends FOSRestController
{
    /**
     * @Route("/{id}", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     * @param FeedbackMapper $mapper
     * @param int $id
     * @return View
     */
    public function getFeedback(FeedbackMapper $mapper, int $id) : View
    {
        /** @var Feedback[] $feedbacks */
        $feedbacks = $this->getDoctrine()->getRepository(Feedback::class)
            ->findBy(['employee' => $id]);
        /** @var User $user */
        $user = $this->getUser();
        /** @var Employee $loggedEmployee */
        $loggedEmployee = $user->getEmployee();
        if ($loggedEmployee->getId() === $id) {
            $response = array_map($mapper, $feedbacks);
            return $this->view($response, Response::HTTP_OK);
        } else {
            $leaderId = $loggedEmployee->getId();
            /** @var Employee $employee */
            $employee = $this->getDoctrine()->getRepository(Employee::class)->find($id);
            if ($employee->isLeader($leaderId)
                || ($loggedEmployee->isTribeMaster() && $loggedEmployee->getTribe() === $employee->getTribe())
                || $loggedEmployee->getTribeResponsible()->contains($employee->getTribe())
                || $loggedEmployee->getUser()->hasRole('ROLE_SUPER_ADMIN')) {
                $response = array_map($mapper, $feedbacks);
                return $this->view($response, Response::HTTP_OK);
            } else {
                throw new AccessDeniedHttpException(sprintf(
                    "You are not allowed to get feedbacks for employee with ID %d",
                    $id
                ));
            }
        }
    }

    /**
     * @Route("/provided/{id}", requirements={"id": "\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     * @param FeedbackMapper $mapper
     * @param int $id
     * @return View
     */
    public function getFeedbackProvided(FeedbackMapper $mapper, int $id) : View
    {
        /** @var Feedback[] $feedbacks */
        $feedbacks = $this->getDoctrine()->getRepository(Feedback::class)
            ->findBy(['leader' => $id]);
        /** @var User $user */
        $user = $this->getUser();
        /** @var Employee $loggedEmployee */
        $loggedEmployee = $user->getEmployee();
        if ($loggedEmployee->getId() === $id) {
            $response = array_map($mapper, $feedbacks);
            return $this->view($response, Response::HTTP_OK);
        } else {
            throw new AccessDeniedHttpException(sprintf(
                "You are not allowed to get feedbacks for leader with ID %d",
                $id
            ));
        }
    }

    /**
     * @Route("")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function addFeedback(Request $request, MessageBusInterface $messageBus): View
    {
        /** @var User $user */
        $user = $this->getUser();
        $leader = $user->getEmployee();
        $message = new CreateFeedback(
            $request->get('employeeId'),
            $leader,
            $request->get('feedback', null),
            $request->get('progressFeedback', null),
            $request->get('technicalFeedback', null),
            $request->get('type'),
            $request->get('dateCreated'),
        );
        try {
            $messageBus->dispatch($message);
            return $this->view([], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/{id}", requirements={"id": "\d+"})
     * @Method("PATCH")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param MessageBusInterface $messageBus
     * @param int $id
     * @return View
     */
    public function updateFeedback(Request $request, MessageBusInterface $messageBus, int $id): View
    {
        /** @var Employee $employee */
        $employee = $this->getUser()->getEmployee();
        $message = new UpdateFeedback(
            $id,
            $employee,
            $request->get('feedback', null),
            $request->get('progressFeedback', null),
            $request->get('technicalFeedback', null),
            $request->get('dateCreated', null)
        );
        try {
            $messageBus->dispatch($message);
            return $this->view([], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/{id}", requirements={"id": "\d+"})
     * @Method("DELETE")
     * @Security("has_role('ROLE_USER')")
     * @param int $id
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function deleteFeedback(MessageBusInterface $messageBus, int $id): View
    {
        /** @var Employee $employee */
        $employee = $this->getUser()->getEmployee();
        $message = new DeleteFeedback($id, $employee);
        try {
            $messageBus->dispatch($message);
            return $this->view([], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/structure/{id}", requirements={"id": "\d+"})
     * @Method("GET")
     * @param LeaderStructureMapper $mapper
     * @param int $id
     * @return View
     */
    public function getMyStructure(LeaderStructureMapper $mapper, int $id) : View
    {
        $user = $this->getUser();
        /** @var Employee $loggedEmployee */
        $loggedEmployee = $user->getEmployee();
        if ($loggedEmployee->getId() === $id) {
            $structure = $loggedEmployee->getLeaderStructures()->getValues();
            $result = $mapper($structure);
            return $this->view($result, Response::HTTP_OK);
        } else {
            throw new AccessDeniedHttpException(sprintf(
                "You are not allowed to get leader structure for leader with ID %d",
                $id
            ));
        }
    }

    /**
     * @Route("/tribe/structure/{id}", requirements={"id": "\d+"})
     * @Method("GET")
     * @param LeaderStructureMapper $mapper
     * @param int $id
     * @return View
     */
    public function getTribeStructure(LeaderStructureMapper $mapper, int $id) : View
    {
        $user = $this->getUser();
        /** @var Employee $loggedEmployee */
        $loggedEmployee = $user->getEmployee();
        if ($loggedEmployee->getId() === $id) {
            /** @var Tribe|null $tribe */
            $tribe = $this->getDoctrine()->getRepository(Tribe::class)
                ->find($loggedEmployee->getTribe()->getId());
            if ($tribe !== null) {
                $tribeEmployees = $tribe->getEmployees()->toArray();
                $result = $mapper($tribeEmployees);
                return $this->view($result, Response::HTTP_OK);
            }
            return $this->view([], Response::HTTP_OK);
        } else {
            throw new AccessDeniedHttpException(sprintf(
                "You are not allowed to get tribe structure for tech leader with ID %d",
                $id
            ));
        }
    }
}
