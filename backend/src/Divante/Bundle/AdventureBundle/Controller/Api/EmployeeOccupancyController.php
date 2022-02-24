<?php

namespace Divante\Bundle\AdventureBundle\Controller\Api;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\EmployeeOccupancy;
use Divante\Bundle\AdventureBundle\Entity\Project;
use Divante\Bundle\AdventureBundle\Mappers\OccupancyMapper;
use Divante\Bundle\AdventureBundle\Message\CreateOccupancyEntry;
use Divante\Bundle\AdventureBundle\Message\DeleteOccupancyEntry;
use Divante\Bundle\AdventureBundle\Message\UpdateOccupancyEntry;
use Divante\Bundle\AdventureBundle\Query\Pairings\PairingsQuery;
use Divante\Bundle\AdventureBundle\Query\Pairings\PairingsView;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\View\View;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Project skill area controller.
 *
 * @Route("api/pairings")
 */
class EmployeeOccupancyController extends FOSRestController
{
    private MessageBusInterface $messageBus;
    private OccupancyMapper $mapper;
    private PairingsQuery $query;

    public function __construct(MessageBusInterface $messageBus, OccupancyMapper $mapper, PairingsQuery $query)
    {
        $this->messageBus = $messageBus;
        $this->mapper = $mapper;
        $this->query = $query;
    }

    /**
     * Creates a new employee occupancy for project entity.
     *
     * @Route("", name="employeeoccupancy_new")
     * @Method("POST")
     * @Security("has_role('ROLE_MANAGER')")
     * @param Request $request
     * @return View
     */
    public function newAction(Request $request): View
    {
        try {
            $data = $request->get('data', []);
            foreach ($data as $entry) {
                $this->pushData($entry);
            }
            return $this->view([], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param array<string,mixed> $data
     * @return PairingsView
     */
    private function pushData(array $data) : PairingsView
    {
        $employeeId = $data['employeeId'] ?? -1;
        $projectId = $data['projectId'] ?? -1;
        $timestamp = $data['timestamp'] ?? -1;
        $secondsPerDay = $data['secondsPerDay'] ?? -1;
        $createEntry = new CreateOccupancyEntry(
            $employeeId,
            $projectId,
            $secondsPerDay,
            $timestamp
        );
        $this->messageBus->dispatch($createEntry);
        $entry = $this->query->getByData([
            'employee_id' => $employeeId,
            'project_id' => $projectId,
            'occupancy' => $secondsPerDay,
            'date' => $timestamp
        ]);
        return $entry;
    }

    /**
     * Edit an existing employee occupancy day.
     *
     * @Route("/{id}", name="employeeoccupancy_edit")
     * @Method("PATCH")
     *
     * @Security("has_role('ROLE_MANAGER')")
     *
     * @ApiDoc(
     *  resource=true,
     *  section="EmployeeOccupancy",
     *  description="Edit an existing employee occupancy day",
     *  output="Divante\Bundle\AdventureBundle\Entity\EmployeeOccupancy",
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="Employee occupancy day id"}
     *  },
     *  parameters={
     *      {"name"="employee", "dataType"="integer", "required"=true, "description"="Employee id"},
     *      {"name"="project", "dataType"="integer", "required"=true, "description"="Project id"},
     *      {"name"="data", "dataType"="integer", "required"=true, "description"="Day date"},
     *      {"name"="occupancy", "dataType"="integer", "required"=true, "description"="Occupancy time"}
     *  },
     *  statusCodes={
     *         200="Returned project skill area entity when successful",
     *         400="Bad request",
     *         403="Forbidden for this user",
     *         404="Project skill area not found",
     *         405="Method not allowed",
     *  }
     * )
     * @param Request $request
     * @param int $id
     *
     * @return View
     */
    public function editAction(Request $request, int $id): View
    {
        $em = $this->getDoctrine()->getManager();
        try {
            $employeeId = $request->get('employeeId', -1);
            $projectId = $request->get('projectId', -1);
            $timestamp = $request->get('timestamp', null);
            $secondsPerDay = $request->get('secondsPerDay', null);

            /** @var EmployeeOccupancy|null $entry */
            $entry = $em->getRepository(EmployeeOccupancy::class)->find($id);
            /** @var Employee|null $employee */
            $employee = $em->getRepository(Employee::class)->find($employeeId);
            /** @var Project|null $project */
            $project = $em->getRepository(Project::class)->find($projectId);

            $updateEntry = new UpdateOccupancyEntry(
                $entry,
                $employee,
                $project,
                $secondsPerDay,
                $timestamp
            );
            $this->messageBus->dispatch($updateEntry);

            return $this->view($this->mapper->mapEntity($entry), Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Deletes a day of employee occupancy in project entity.
     *
     * @Route("/{id}", name="deleteoccupancy")
     * @Method("DELETE")
     *
     * @Security("has_role('ROLE_MANAGER')")
     *
     * @ApiDoc(
     *  resource=true,
     *  section="EmployeeOccupancy",
     *  description="Deletes a day of employee occupancy in project entity",
     *  requirements={
     *      {"name"="id", "dataType"="integer", "requirement"="\d+", "description"="employee occupancy entry id"}
     *  },
     *  statusCodes={
     *         200="Returned when successful",
     *         400="Bad request",
     *         403="Forbidden for this user",
     *         404="Project skill area not found",
     *         405="Method not allowed",
     *  }
     * )
     * @param int $id
     *
     * @return View
     */
    public function deleteAction(int $id): View
    {
        $message = new DeleteOccupancyEntry($id);
        try {
            $this->messageBus->dispatch($message);
            return $this->view([], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("", name="pairings_index")
     * @Security("has_role('ROLE_MANAGER')")
     * @Method("GET")
     * @param Request $request
     * @return View
     */
    public function indexAction(Request $request)
    {
        try {
            $pairing = $this->query->getByTimestamps(
                $request->query->get('timestamp_gte'),
                $request->query->get('timestamp_lte')
            );
            return $this->view($pairing, Response::HTTP_OK);
        } catch (\Exception $exception) {
            return $this->view($exception, Response::HTTP_BAD_REQUEST);
        }
    }
}
