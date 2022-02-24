<?php

namespace Divante\Bundle\AdventureBundle\Controller\Api\Feedback;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Feedback\PlannedFeedback;
use Divante\Bundle\AdventureBundle\Mappers\Feedback\FeedbackPlannedMapper;
use Divante\Bundle\AdventureBundle\Message\Feedback\CreatePlannedFeedback;
use Divante\Bundle\AdventureBundle\Entity\User;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Class PlannedFeedbackController
 * @package Divante\Bundle\AdventureBundle\Controller\Api\Feedback
 * @Route("api/feedback/planned")
 */
class PlannedFeedbackController extends FOSRestController
{
    /**
     * @Route("")
     * @Method("POST")
     * @param Request $request
     * @return View
     */
    public function newEntryAction(Request $request, MessageBusInterface $messageBus): View
    {
        /** @var int|null $id */
        $id = $request->get("employeeId");
        /** @var string|null $date */
        $date = $request->get("date");
        if (is_null($id) || is_null($date)) {
            return $this->view(['error' => "Fields 'employeeId' and 'date' are required"], Response::HTTP_BAD_REQUEST);
        }
        /** @var User $user */
        $user = $this->getUser();
        /** @var int $employeeId */
        $employeeId = $user->getEmployeeId();
        $message = new CreatePlannedFeedback($id, $employeeId, $date);
        $messageBus->dispatch($message);
        return $this->view([], Response::HTTP_OK);
    }

    /**
     * @Route("/employee/{id}", requirements={"id": "\d+"})
     * @Method("GET")
     * @param FeedbackPlannedMapper $mapper
     * @param int $id
     * @return View
     */
    public function getPlannedByEmployee(FeedbackPlannedMapper $mapper, int $id): View
    {
        /** @var Employee $employee */
        $employee = $this->getDoctrine()->getRepository(Employee::class)->find($id);
        /** @var User $user */
        $user = $this->getUser();
        /** @var Employee $userEmployee */
        $userEmployee = $user->getEmployee();
        $leaderId = $userEmployee->getId();
        if (!$employee->isLeader($leaderId) && $employee->getId() !== $userEmployee->getId()
            && !$userEmployee->getTribeResponsible()->contains($employee->getTribe())) {
            throw new AccessDeniedHttpException("You are not allowed to see planned feedbacks for this person");
        }
        /** @var PlannedFeedback[] $feedbacks */
        $feedbacks = $this->getDoctrine()->getRepository(PlannedFeedback::class)
            ->findBy(['employee' => $id]);
        $response = array_map($mapper, $feedbacks);
        return $this->view($response, Response::HTTP_OK);
    }
}
