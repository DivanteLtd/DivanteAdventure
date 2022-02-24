<?php

namespace Divante\Bundle\AdventureBundle\Controller\Api;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\EmployeeProject;
use Divante\Bundle\AdventureBundle\Entity\Notification;
use Divante\Bundle\AdventureBundle\Entity\Project;
use Divante\Bundle\AdventureBundle\Mappers\EmployeeProjectMapper;
use Divante\Bundle\AdventureBundle\Message\Dashboard\CreateNotification;
use Divante\Bundle\AdventureBundle\Message\Project\CreatePairing;
use Divante\Bundle\AdventureBundle\Message\Project\UpdatePairing;
use Divante\Bundle\AdventureBundle\Message\Project\DeleteEmployeeProject;
use Divante\Bundle\AdventureBundle\Query\EmployeeProject\EmployeeProjectQuery;
use Exception;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Employee project controller.
 *
 * @Route("api/employeeProjects")
 */
class EmployeeProjectController extends FOSRestController
{
    /** @var EmployeeProjectMapper */
    private $mapper;
    /** @var MessageBusInterface */
    private $messageBus;
    /** @var EmployeeProjectQuery */
    private $query;

    public function __construct(
        EmployeeProjectMapper $mapper,
        MessageBusInterface $messageBus,
        EmployeeProjectQuery $query
    ) {
        $this->mapper = $mapper;
        $this->messageBus = $messageBus;
        $this->query = $query;
    }

    /**
     * Lists all employee project entities.
     *
     * Access: ADMIN, USER
     *
     * @Route("", name="employeeproject_index")
     * @Method("GET")
     *
     * @Security("has_role('ROLE_USER')")
     *
     * @ApiDoc(
     *  section="EmployeeProject",
     *  resource=true,
     *  description="Gets all employee projects",
     *  output="Divante\Bundle\AdventureBundle\Entity\EmployeeProject",
     * )
     *
     * @return View
     */
    public function indexAction() : View
    {
        try {
            $cachedPairs = $this->get('cache.app')->getItem('pairs');
            if (!$cachedPairs->isHit()) {
                $pairs = $this->query->getAll();
                $cachedPairs->set($pairs);
                $this->get('cache.app')->save($cachedPairs);
            } else {
                $pairs = $cachedPairs->get();
            }

            return $this->view($pairs, Response::HTTP_OK);
        } catch (Exception $exception) {
            return $this->view($exception, Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("", name="employeeproject_create")
     * @Method("POST")
     * @Security("has_role('ROLE_MANAGER')")
     * @param Request $request
     * @return View
     */
    public function createPairingAction(Request $request) : View
    {
        $employeeId = (int)$request->get('employeeId', -1);
        $projectId = (int)$request->get('projectId', -1);
        $dateFrom = $request->get('dateFrom', []);
        $dateTo = $request->get('dateTo', []);
        $overtime = (bool)$request->get('overtime', false);
        $createPairingMessage = new CreatePairing(
            $employeeId,
            $projectId,
            $dateFrom,
            $dateTo,
            $overtime
        );
        $em = $this->getDoctrine()->getManager();
        $project = $em->getRepository(Project::class)->find($projectId);
        $path = Notification::PROJECT_PATH.$projectId;
        $description = Notification::USER_PROJECT_NEW;
        $subject = $project->getName();
        $createEntry = new CreateNotification($employeeId, $description, $subject, $path);
        try {
            $this->messageBus->dispatch($createPairingMessage);
            $employee = $em->getRepository(Employee::class)->find($employeeId);
            $createdEntry = $em->getRepository(EmployeeProject::class)->findOneBy([
                'employee' => $employee,
                'project' => $project
            ]);
            $this->get('cache.app')->deleteItem('pairs');
            $this->messageBus->dispatch($createEntry);
            return $this->view([
                'id'               => $createdEntry->getId(),
                'employeeId'       => $employeeId,
                'projectId'        => $projectId,
                'employeeName'     => $employee->getName(),
                'employeeLastName' => $employee->getLastName(),
                'projectName'      => $project->getName(),
                'dateFrom'         => $dateFrom,
                'dateTo'           => $dateTo,
                'overtime'         => $overtime
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/{id}", name="employeeproject_update")
     * @Security("has_role('ROLE_MANAGER')")
     * @Method("PATCH")
     * @param Request $request
     * @param int $id
     * @return View
     */
    public function updateAction(Request $request, int $id) :View
    {
        $em = $this->getDoctrine()->getManager();
        /** @var EmployeeProject|null $employeeProject */
        $employeeProject = $em->getRepository(EmployeeProject::class)->find($id);
        if (is_null($employeeProject)) {
            return $this->view(['error' => "Pairing #$id has not been found"], Response::HTTP_NOT_FOUND);
        }
        $employeeId = $employeeProject->getEmployee()->getId();
        $projectId = $employeeProject->getProject()->getId();
        $dateFrom = (array)$request->get('dateFrom', []);
        $dateTo = (array)$request->get('dateTo', []);
        $overtime = (bool)$request->get('overtime', false);
        $updateMessage = new UpdatePairing($id, $dateFrom, $dateTo, $overtime, $employeeId, $projectId);
        if ($request->get('deletions')) {
            $description = Notification::USER_PROJECT_DELETED;
        } elseif ($request->get('adding')) {
            $description = Notification::USER_PROJECT_NEW;
        } else {
            $description = Notification::USER_PROJECT_EDITED;
        }
        $path = Notification::PROJECT_PATH.$projectId;
        $subject = $employeeProject->getProject()->getName();
        $createEntry = new CreateNotification($employeeId, $description, $subject, $path);
        try {
            $this->messageBus->dispatch($updateMessage);
            $this->get('cache.app')->deleteItem('pairs');
            $this->messageBus->dispatch($createEntry);
            return $this->view($this->query->getById($id), Response::HTTP_OK);
        } catch (Exception $exception) {
            return $this->view(['error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/{id}", name="deleteemployeeproject")
     * @Security("has_role('ROLE_MANAGER')")
     * @Method("DELETE")
     * @param int $id
     * @return View
     */
    public function deleteAction(int $id) :View
    {
        $em = $this->getDoctrine()->getManager();
        /** @var EmployeeProject|null $employeeProject */
        $employeeProject = $em->getRepository(EmployeeProject::class)->find($id);
        if (is_null($employeeProject)) {
            return $this->view(['error' => "Pairing #$id has not been found"], Response::HTTP_NOT_FOUND);
        }
        $employeeId = $employeeProject->getEmployee()->getId();
        $projectId = $employeeProject->getProject()->getId();
        $message = new DeleteEmployeeProject($id, $employeeId, $projectId);
        $description = Notification::USER_PROJECTS_DELETED;
        $subject = $employeeProject->getProject()->getName();
        $path = Notification::PROJECT_PATH.$projectId;
        $createEntry = new CreateNotification($employeeId, $description, $subject, $path);
        try {
            $this->messageBus->dispatch($message);
            $this->get('cache.app')->deleteItem('pairs');
            $this->messageBus->dispatch($createEntry);
            return $this->view([], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
