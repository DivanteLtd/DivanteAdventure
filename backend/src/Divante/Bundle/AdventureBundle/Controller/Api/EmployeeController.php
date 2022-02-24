<?php

namespace Divante\Bundle\AdventureBundle\Controller\Api;

use DateTime;
use Divante\Bundle\AdventureBundle\Auth\UserPersistor;
use Divante\Bundle\AdventureBundle\Entity\Checklist\Checklist;
use Divante\Bundle\AdventureBundle\Entity\Contract;
use Divante\Bundle\AdventureBundle\Entity\EmployeeWorkLocation;
use Divante\Bundle\AdventureBundle\Entity\Notification;
use Divante\Bundle\AdventureBundle\Entity\Repository\Checklist\ChecklistRepository;
use Divante\Bundle\AdventureBundle\Entity\Repository\EmployeeRepository;
use Divante\Bundle\AdventureBundle\Entity\Repository\WorkLocation\WorkLocationRepository;
use Divante\Bundle\AdventureBundle\Entity\Tribe;
use Divante\Bundle\AdventureBundle\Events\Account\EmployeeDeletedEvent;
use Divante\Bundle\AdventureBundle\Events\SlackAdminLogEvent;
use Divante\Bundle\AdventureBundle\Events\Tribe\TribeAssignmentChangeEvent;
use Divante\Bundle\AdventureBundle\Mappers\Checklist\ChecklistListMapper;
use Divante\Bundle\AdventureBundle\Mappers\Employee\ContractMapper;
use Divante\Bundle\AdventureBundle\Mappers\Employee\LeaderStructuresMapper;
use Divante\Bundle\AdventureBundle\Message\Employee\AddContract;
use Divante\Bundle\AdventureBundle\Message\Employee\DeletePadawan;
use Divante\Bundle\AdventureBundle\Mappers\Hardware\HardwareMapper;
use Divante\Bundle\AdventureBundle\Mappers\Employee\EmployeeMapperFactory;
use Divante\Bundle\AdventureBundle\Mappers\EmployeeRequestMapper;
use Divante\Bundle\AdventureBundle\Mappers\WorkLocationMapper;
use Divante\Bundle\AdventureBundle\Message\Dashboard\CreateNotification;
use Divante\Bundle\AdventureBundle\Message\Employee\AssignEmployeeToTribe;
use Divante\Bundle\AdventureBundle\Message\Employee\CreateEmployeeEndingCooperation;
use Divante\Bundle\AdventureBundle\Message\Employee\CreateEmployeeWorkLocation;
use Divante\Bundle\AdventureBundle\Message\Employee\DeleteEmployeeEndingCooperation;
use Divante\Bundle\AdventureBundle\Message\Employee\CreateLeaderStructure;
use Divante\Bundle\AdventureBundle\Message\Employee\EditContract;
use Divante\Bundle\AdventureBundle\Message\Employee\UnAssignEmployeeToTribe;
use Divante\Bundle\AdventureBundle\Message\Employee\UnlockEmployee;
use Divante\Bundle\AdventureBundle\Message\Employee\UpdateEmployeeEndingCooperation;
use Divante\Bundle\AdventureBundle\Query\Employee\EmployeeEndCooperationQuery;
use Divante\Bundle\AdventureBundle\Query\Employee\EmployeeQuery;
use Divante\Bundle\AdventureBundle\Query\Employee\FirstEmployeeHiredDateQuery;
use Divante\Bundle\AdventureBundle\Query\Role\RoleQuery;
use Divante\Bundle\AdventureBundle\Services\EmployeeRequirement;
use Divante\Bundle\AdventureBundle\Entity\User;
use Divante\Bundle\AdventureBundle\Services\Exporter;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\View;
use Collator;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\Controller\FOSRestController;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Exception;

/**
 * Employee controller.
 *
 * @Route("api/employees")
 */
class EmployeeController extends FOSRestController
{
    private const PIN_VERIFICATION_FAILED = 'not verified';
    private const PIN_ACCOUNT_BLOCKED = 'account blocked';

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Lists all employee entities. Used in planner
     *
     * Access: ADMIN, USER
     *
     * @Route("", name="employee_index")
     * @Method("GET")
     *
     * @Security("has_role('ROLE_MANAGER')")
     * @param Request $request
     * @param EmployeeQuery $employeeQuery
     * @return View
     */
    public function indexAction(Request $request, EmployeeQuery $employeeQuery) : View
    {
        $query = $request->get('query');
        $dissalowStrings = ['SET', 'DROP', 'DELETE', 'UPDATE'];
        foreach ($dissalowStrings as $string) {
            if (strpos(strtoupper($query), $string) !== false) {
                return $this->view(
                    ['error' => sprintf('You can not use %s', $string)],
                    Response::HTTP_BAD_REQUEST
                );
            }
        }
        $employees = $employeeQuery->getAllForSchedulerByQuery($query);
        $coll = new Collator('pl_PL');
        usort($employees, function (object $a, object $b) use ($coll) {
            return $coll->compare($a->getLastName(), $b->getLastName());
        });

        return $this->view($employees, Response::HTTP_OK);
    }

    /**
     * @Route("/details", name="employee_details")
     * @Security("has_role('ROLE_USER')")
     * @Method("GET")
     * @param EmployeeMapperFactory $mapperFactory
     * @return View
     */
    public function listWithDetailsAction(EmployeeMapperFactory $mapperFactory) : View
    {
        return $this->view($this->listEmployees($mapperFactory), Response::HTTP_OK);
    }

    /**
     * @Route("/hardware/{id}", name="employee_hardware")
     * @Method("GET")
     * @param Employee $employee
     * @param HardwareMapper $hardwareMapper
     * @return View
     * @throws \Exception
     */
    public function hardwareList(Employee $employee, HardwareMapper $hardwareMapper) : View
    {
        /** @var User $user */
        $user = $this->getUser();
        $result = [];
        if ($this->isSelf($employee) || $user->hasRole('ROLE_HELPDESK')) {
            $result = $hardwareMapper->mapEntity($employee->getId());
        }
        return $this->view($result, Response::HTTP_OK);
    }

    /**
     * @param EmployeeMapperFactory $employeeMapperFactory
     * @return array<int,array<string,mixed>>
     */
    protected function listEmployees(EmployeeMapperFactory $employeeMapperFactory) : array
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var EmployeeRepository $employeeRepo */
        $employeeRepo = $this->getDoctrine()->getRepository(Employee::class);
        $employees = $employeeRepo->findAllWithoutFormerEmployees();

        $mapper = $employeeMapperFactory->getFor($user);
        $result = array_map(
            fn(Employee $employee) : array => $mapper($employee),
            $employees,
        );
        $coll = new Collator('pl_PL');
        usort($result, function (array $a, array $b) use ($coll) {
            return $coll->compare($a['lastName'], $b['lastName']);
        });
        return $result;
    }

    /**
     * Finds and displays a employee entity.
     * @Route("/id/{id}", name="employee_show")
     * @Method("GET")
     *
     * @Security("has_role('ROLE_USER')")
     * @param Employee $employee
     * @param EmployeeMapperFactory $mapperFactory
     * @param RoleQuery $roleQuery
     * @return View
     */
    public function showAction(
        Employee $employee,
        EmployeeMapperFactory $mapperFactory,
        RoleQuery $roleQuery
    ) : View {
        /** @var User $user */
        $user = $this->getUser();
        $mapper = $this->isSelf($employee) ? $mapperFactory->getForSelf() : $mapperFactory->getFor($user);
        $employeeData = $mapper->mapEmployeeToJson($employee);
        $roles = $roleQuery->getAll();
        return $this->view(
            [
                "employee" => $employeeData,
                'departments' => [],
                'positions' => [],
                'contracts' => [
                    [
                        'id' => Employee::CONTRACT_B2B_LUMP_SUM,
                        'name' => 'B2B LUMP SUM',
                    ],[
                        'id' => Employee::CONTRACT_B2B_HOURLY,
                        'name' => 'B2B HOURLY',
                    ],[
                        'id' => Employee::CONTRACT_CLC_LUMP_SUM,
                        'name' => 'CLC LUMP SUM',
                    ], [
                        'id' => Employee::CONTRACT_CLC_HOURLY,
                        'name' => 'CLC HOURLY',
                    ], [
                        'id' => Employee::CONTRACT_COE,
                        'name' => 'CoE',
                    ],
                ],
                'roles' => $roles,
                'askForSlack' => $employee->getSlackStatus() === Employee::SLACK_STATUS_NOT_ASKED,
            ],
            Response::HTTP_OK
        );
    }

    /**
     * Delete a employee entity.
     *
     * Access: SUPER_ADMIN
     *
     * @Route("/{id}", name="employee_delete")
     * @Method("DELETE")
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param Employee $employee
     * @param EventDispatcherInterface $eventDispatcher
     * @return View
     * @throws \Exception
     */
    public function deleteAction(Employee $employee, EventDispatcherInterface $eventDispatcher) : View
    {
        $em = $this->getDoctrine()->getManager();
        $employee->setHiredTo(new DateTime());
        $tribeId = $employee->getTribe()->getId();
        if (!$em->contains($employee)) {
            $em->persist($employee);
        }
        /** @var User $user */
        $user = $this->getUser();
        $eventDispatcher->dispatch(new EmployeeDeletedEvent($employee, $user->getEmployee()));
        $eventDispatcher->dispatch(new TribeAssignmentChangeEvent($employee->getId(), $tribeId));
        $em->flush();
        try {
            $this->get('cache.app')->deleteItem('employees');
        } catch (InvalidArgumentException $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->view([], Response::HTTP_OK);
    }

    /**
     * @Route("/isPinSet", name="employee_pin_isset")
     * @Method("GET")
     * @param EmployeeRequirement $requirement
     * @return View
     */
    public function checkPinSet(EmployeeRequirement $requirement) : View
    {
        /** @var User|mixed $user */
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->view([], Response::HTTP_UNAUTHORIZED);
        }
        $employee = $user->getEmployee();
        $data = [
            'hasSetPin' => $requirement->hasSetPin($employee),
        ];
        return $this->view($data, Response::HTTP_OK);
    }

    /**
     * @Route("/verifyPin", name="employee_pin_verify")
     * @Method("POST")
     * @param Request $request
     * @param UserPersistor $persistor
     * @return View
     * @throws \Exception
     */
    public function verifyPinNumber(Request $request, UserPersistor $persistor) : View
    {
        /** @var User|mixed $user */
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->view([], Response::HTTP_UNAUTHORIZED);
        }
        $employee = $user->getEmployee();
        /** @var string|null $pin */
        $pin = $request->get('pin');
        if (is_null($pin)) {
            return $this->view(['error' => 'no PIN number supplied'], Response::HTTP_BAD_REQUEST);
        } elseif ($employee->isPinLocked()) {
            $persistor->sendEmailToBlockedUser($user);
            return $this->view(['message' => self::PIN_ACCOUNT_BLOCKED], Response::HTTP_UNAUTHORIZED);
        } elseif ($employee->validatePin($pin)) {
            return $this->view([], Response::HTTP_OK);
        } else {
            $message = $employee->isPinLocked() ? self::PIN_ACCOUNT_BLOCKED : self::PIN_VERIFICATION_FAILED;
            $this->getDoctrine()->getManager()->flush();
            if ($employee->isPinLocked()) {
                $persistor->sendEmailToBlockedUser($user);
                $slackMessage = sprintf(
                    "*%s %s* - account blocked",
                    $employee->getName(),
                    $employee->getLastName(),
                );
                $this->eventDispatcher->dispatch(new SlackAdminLogEvent($slackMessage));
            }
            return $this->view(['message' => $message], Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @Route("/unlock/{id}", name="employee_unlock")
     * @Method("POST")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param int $id
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function unlockUser(int $id, MessageBusInterface $messageBus) : View
    {
        $message = new UnlockEmployee($id);
        try {
            $messageBus->dispatch($message);
        } catch (\Exception $e) {
            return $this->view(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
        return $this->view([], Response::HTTP_OK);
    }

    /**
     * @Route("/{id}", name="employee_update")
     * @Method("PATCH")
     * @Security("is_granted('EMPLOYEE_EDIT', employee)")
     * @param Request $request
     * @param Employee $employee
     * @param EmployeeRequestMapper $requestMapper
     * @param EmployeeMapperFactory $mapperFactory
     * @param MessageBusInterface $messageBus
     * @return View
     * @throws \Exception
     */
    public function updateAction(
        Request $request,
        Employee $employee,
        EmployeeRequestMapper $requestMapper,
        EmployeeMapperFactory $mapperFactory,
        MessageBusInterface $messageBus
    ) : View {
        try {
            /** @var User $user */
            $user = $this->getUser();
            $userEmployee = $user->getEmployee();
            $message = $requestMapper->mapToMessage($request);
            $message->setCallingEmployee($userEmployee);
            $messageBus->dispatch($message);
            $employee = $this->findEmployeeById($employee->getId());
            $m = $this->isSelf($employee) ? $mapperFactory->getForSelf() : $mapperFactory->getFor($this->getUser());
            $employeeData = $m->mapEmployeeToJson($employee);
            $this->get('cache.app')->deleteItem('employees');
        } catch (InvalidArgumentException $e) {
            return $this->view(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Exception $exception) {
            return $this->view(['message' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->view(
            $employeeData,
            Response::HTTP_OK
        );
    }

    /**
     * @Route("/assign/tribe/{id}", name="employee_assign_to_tribe")
     * @Method("POST")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @param Employee $employee
     * @param MessageBusInterface $messageBus
     * @param EmployeeMapperFactory $mapperFactory
     * @return View
     */
    public function assignToTribeAction(
        EntityManagerInterface $em,
        Request $request,
        Employee $employee,
        MessageBusInterface $messageBus,
        EmployeeMapperFactory $mapperFactory
    ) : View {
        /** @var User $user */
        $user = $this->getUser();
        if ($this->isSelf($employee) || $user->hasRole('ROLE_TRIBE_MASTER')) {
            return $this->tribeAssignmentView(true, $em, $request, $employee->getId(), $messageBus, $mapperFactory);
        } else {
            throw new AccessDeniedHttpException(sprintf(
                "You are not allowed to update data for employee with ID %d",
                $employee->getId()
            ));
        }
    }

    /**
     * @Route("/unassign/tribe/{id}", name="employee_unassign_to_tribe")
     * @Method("POST")
     * @param EntityManagerInterface $em
     * @param Request $request `
     * @param Employee $employee
     * @param MessageBusInterface $messageBus
     * @param EmployeeMapperFactory $mapperFactory
     * @return View
     */
    public function unassignToTribeAction(
        EntityManagerInterface $em,
        Request $request,
        Employee $employee,
        MessageBusInterface $messageBus,
        EmployeeMapperFactory $mapperFactory
    ) : View {
        /** @var User $user */
        $user = $this->getUser();
        if ($this->isSelf($employee) || $user->hasRole('ROLE_TRIBE_MASTER')) {
            return $this->tribeAssignmentView(false, $em, $request, $employee->getId(), $messageBus, $mapperFactory);
        } else {
            throw new AccessDeniedHttpException(sprintf(
                "You are not allowed to update data for employee with ID %d",
                $employee->getId()
            ));
        }
    }

    private function tribeAssignmentView(
        bool $assign,
        EntityManagerInterface $em,
        Request $request,
        int $id,
        MessageBusInterface $messageBus,
        EmployeeMapperFactory $mapperFactory
    ) : View {
        $userId = $id;
        $tribeId = $request->get('tribe_id', null);
        $message = $assign
            ? new AssignEmployeeToTribe($userId, $tribeId)
            : new UnAssignEmployeeToTribe($userId, $tribeId);
        $messageBus->dispatch($message);
        $path = Notification::TRIBE_PATH.$tribeId;
        $description = $assign ? Notification::USER_TRIBE_ADD : Notification::USER_TRIBE_DELETE;
        $tribe = $em->getRepository(Tribe::class)->find($tribeId);
        $subject = $tribe->getName();
        $createEntry = new CreateNotification($userId, $description, $subject, $path);
        $messageBus->dispatch($createEntry);
        return $this->view($this->listEmployees($mapperFactory), Response::HTTP_OK);
    }

    /**
     * @Route("/endedWork", name="get_employee_ending_cooperation_list")
     * @Method("GET")
     * @Security("has_role('ROLE_HR')")
     * @param EmployeeEndCooperationQuery $query
     * @return View
     */
    public function getEmployeeEndingCooperationAction(EmployeeEndCooperationQuery $query) :View
    {
        $employees = $query->getAll();
        $coll = new Collator('pl_PL');
        usort($employees, function (object $a, object $b) use ($coll) {
            return $coll->compare($a->getLastName(), $b->getLastName());
        });
        return $this->view($employees, Response::HTTP_OK);
    }

    /**
     * Get date when first employee was hired
     *
     * @Route("/firstHiredDate", name="get_first_hired_date")
     * @Method("GET")
     * @param FirstEmployeeHiredDateQuery $query
     * @return View
     */
    public function getFirstHiredDate(FirstEmployeeHiredDateQuery $query) :View
    {
        return $this->view($query->getFirstHiredDate(), Response::HTTP_OK);
    }

    /**
     * @Route("/endedWork", name="add_no_workers")
     * @Method("POST")
     * @Security("has_role('ROLE_HR')")
     * @param Request $request
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function addEmployeeEndingCooperation(Request $request, MessageBusInterface $messageBus) : View
    {
        $employeeEmail = $request->get('email');
        $nextCompany = $request->get('nextCompany');
        $whoEndedCooperation = $request->get('whoEndedCooperation');
        $exitInterview = $request->get('exitInterview', false);
        $checklist = $request->get('checklist', false);
        $comment = $request->get('comment');
        $dismissDate = $request->get('dismiss');
        try {
            $message = new CreateEmployeeEndingCooperation(
                $employeeEmail,
                $nextCompany,
                $whoEndedCooperation,
                $exitInterview,
                $checklist,
                $comment,
                $dismissDate
            );
            $messageBus->dispatch($message);
            return $this->view([], Response::HTTP_OK);
        } catch (\Exception $exception) {
            return $this->view(['error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/endedWork/{id}", name="edit_no_workers")
     * @Method("PATCH")
     * @Security("has_role('ROLE_HR')")
     * @param Request $request
     * @param int $id
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function editEmployeeEndingCooperation(Request $request, int $id, MessageBusInterface $messageBus) :View
    {
        $employeeId = $request->get('employeeId');
        $nextCompany = $request->get('nextCompany');
        $whoEndedCooperation = $request->get('whoEndedCooperation');
        $exitInterview = $request->get('exitInterview');
        $checklist = $request->get('checklist');
        $comment = $request->get('comment');
        $dismissDate = $request->get('dismiss');
        try {
            $message = new UpdateEmployeeEndingCooperation(
                $id,
                $employeeId,
                $nextCompany,
                $whoEndedCooperation,
                $exitInterview,
                $checklist,
                $comment,
                $dismissDate
            );
            $messageBus->dispatch($message);
            return $this->view([], Response::HTTP_OK);
        } catch (\Exception $exception) {
            return $this->view(['error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/endedWork/{id}", name="delete_no_workers")
     * @Method("DELETE")
     * @Security("has_role('ROLE_HR')")
     * @param int $id
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function deleteEmployeeEndingCooperation(int $id, MessageBusInterface $messageBus) :View
    {
        $message = new DeleteEmployeeEndingCooperation($id);
        try {
            $messageBus->dispatch($message);
        } catch (\Exception $ex) {
            return $this->view(['error' => $ex->getMessage()], Response::HTTP_BAD_REQUEST);
        }
        return $this->view([], Response::HTTP_OK);
    }

    /**
     * @Route("/hideSlack", name="employee_hide_slack")
     * @Method("POST")
     * @return View
     * @throws \Exception
     */
    public function hideSlackDialog() : View
    {
        /** @var User $user */
        $user = $this->getUser();
        $employee = $user->getEmployee();
        $employee->setSlackStatus(Employee::SLACK_STATUS_ASKED);
        $this->getDoctrine()->getManager()->flush();
        return $this->view([], Response::HTTP_OK);
    }

    /**
     * Save employee work location (remote / trip) in period time
     *
     * @Route("/workLocation", name="employee_work_location_save")
     * @Method("POST")
     * @param Request $request
     * @param MessageBusInterface $messageBus
     * @return View
     * @throws \Exception
     */
    public function saveEmployeeWorkLocation(Request $request, MessageBusInterface $messageBus): View
    {
        $userId = $this->getUser()->getEmployeeId();
        $type = $request->get('type');
        $dates = $request->get('dates');
        $managers = $request->get('managers');
        $message = new CreateEmployeeWorkLocation($userId, $type, $dates, $managers);
        try {
            $messageBus->dispatch($message);
            return $this->view([], Response::HTTP_OK);
        } catch (\Exception $ex) {
            return $this->view(['error' => $ex->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * List employee work locations
     *
     * @Route("/workLocation", name="employee_work_location_get")
     * @Method("GET")
     *
     * @return View
     */
    public function getEmployeeWorkLocations(WorkLocationMapper $mapper): View
    {
        try {
            $userId = $this->getUser()->getEmployeeId();
            $em = $this->getDoctrine()->getManager();
            /** @var EmployeeWorkLocation[] $employeeWorkLocations */
            $employeeWorkLocations = $em->getRepository(EmployeeWorkLocation::class)
                ->findBy(['employeeId' => $userId]);
            $result = array_map($mapper, $employeeWorkLocations);
            return $this->view($result, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * List all employees work locations
     *
     * @Route("/workLocation/all", name="employee_all_work_location_get")
     * @Method("GET")
     *
     * @return View
     */
    public function getAllEmployeesWorkLocations(): View
    {
        try {
            $em = $this->getDoctrine()->getManager();
            /** @var EmployeeWorkLocation[] $allEmployeeWorkLocations */
            $allEmployeeWorkLocations = $em->getRepository(EmployeeWorkLocation::class)->findAll();
            return $this->view($allEmployeeWorkLocations, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
    /**
     * List all employees work locations for today
     * @Route("/workLocation/today", name="employee_today_work_location")
     * @Method("GET")
     * @return View
     * @throws \Exception
     */
    public function getEmployeesTodayWorkLocations(): View
    {
        try {
            $em = $this->getDoctrine()->getManager();
            /** @var WorkLocationRepository $workLocationsRepo */
            $workLocationsRepo = $em->getRepository(EmployeeWorkLocation::class);
            $employeesTodayWorkLocations = $workLocationsRepo->queryEmployeeTodayRecord();
            return $this->view($employeesTodayWorkLocations, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/{id}/checklists", name="employee_checklist_lists", requirements={"id"="\d+"})
     * @Method("GET")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param int $id
     * @param ChecklistListMapper $checklistMapper
     * @return View
     */
    public function getEmployeeChecklists(int $id, ChecklistListMapper $checklistMapper) : View
    {
        $employee = $this->findEmployeeById($id);
        $em = $this->getDoctrine()->getManager();
        /** @var ChecklistRepository $checklistRepo */
        $checklistRepo = $em->getRepository(Checklist::class);
        /** @var Checklist[] $checklists */
        $checklists = $checklistRepo->findByOwnerOrSubject($employee);
        $result = array_map($checklistMapper, $checklists);
        return $this->view($result, Response::HTTP_OK);
    }

    /**
     * @Route("/leaderStructure", name="leader_structure")
     * @Method("POST")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param Request $request
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function addLeaderStructure(Request $request, MessageBusInterface $messageBus) : View
    {
        $leaderId = $request->get('leader', -1);
        $leaderStructure = $request->get('leaderStructure', []);
        $message = new CreateLeaderStructure($leaderId, $leaderStructure);
        try {
            $messageBus->dispatch($message);
            return $this->view([], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/leaderStructures", name="leader_structures")
     * @Method("GET")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param LeaderStructuresMapper $leaderStructureMapper
     * @return View
     */
    public function getLeaderStructures(LeaderStructuresMapper $leaderStructureMapper) : View
    {
        $em = $this->getDoctrine()->getManager();
        /** @var EmployeeRepository $employeeRepo */
        $employeeRepo = $em->getRepository(Employee::class);
        $employees = $employeeRepo->findAllWithoutFormerEmployees();
        $coll = new Collator('pl_PL');
        usort($employees, function (object $a, object $b) use ($coll) {
            return $coll->compare($a->getLastName(), $b->getLastName());
        });
        $structure = $leaderStructureMapper->mapLeaderStructuresToJson($employees);
        return $this->view($structure, Response::HTTP_OK);
    }

    /**
     * @Route("/deletePadawan", name="delete_padawan")
     * @Method("POST")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param Request $request
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function deletePadawan(Request $request, MessageBusInterface $messageBus) : View
    {
        $leaderId = $request->get('leaderId', -1);
        $padawanId = $request->get('padawanId', -1);
        $message = new DeletePadawan($leaderId, $padawanId);
        try {
            $messageBus->dispatch($message);
            return $this->view([], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    private function findEmployeeById(int $id) : Employee
    {
        $em = $this->getDoctrine()->getManager();
        $employeeRepo = $em->getRepository(Employee::class);
        /** @var Employee|null $employee */
        $employee = $employeeRepo->find($id);
        if (is_null($employee)) {
            throw new NotFoundHttpException("Employee with ID $id not found");
        }
        return $employee;
    }

    private function findContract(int $id) :Contract
    {
        $em = $this->getDoctrine()->getManager();
        $contractRepo = $em->getRepository(Contract::class);
        /** @var Contract|null $contract */
        $contract = $contractRepo->find($id);
        if (is_null($contract)) {
            throw new NotFoundHttpException("Contract with ID $id not found");
        }
        return $contract;
    }

    private function isSelf(Employee $employee) : bool
    {
        /** @var User $user */
        $user = $this->getUser();
        return $user->getEmployee()->getId() === $employee->getId();
    }

    /**
     * @Route("/addContract/{id}", name="add_contract")
     * @Method("POST")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     *
     * @param int $id
     * @param Request $request
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function addContract(int $id, Request $request, MessageBusInterface $messageBus) :View
    {
        $params = $this->getParamsForContract($request);
        $message = new AddContract(
            $params['typeId'],
            $params['employeeId'],
            $params['startDate'],
            $params['endDate'],
            $params['assignDate'],
            $params['noticePeriod'],
            $params['active']
        );
        $messageBus->dispatch($message);
        return $this->view([], Response::HTTP_OK);
    }

    /**
     * @Route("/editContract/{id}", name="edit_contract")
     * @Method("POST")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     *
     * @param int $id
     * @param Request $request
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function editContract(int $id, Request $request, MessageBusInterface $messageBus) :View
    {
        $params = $this->getParamsForContract($request);
        $message = new EditContract(
            $id,
            $params['typeId'],
            $params['employeeId'],
            $params['startDate'],
            $params['endDate'],
            $params['assignDate'],
            $params['noticePeriod'],
            $params['active']
        );
        $messageBus->dispatch($message);
        return $this->view([], Response::HTTP_OK);
    }
    /**
     * @Route("/deleteContract/{id}", name="delete_contract")
     * @Method("DELETE")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     *
     * @param int $id
     * @param Request $request
     * @return View
     */
    public function deleteContract(int $id, Request $request) :View
    {
        $contract = $this->findContract($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($contract);
        $em->flush();
        return $this->view([], Response::HTTP_OK);
    }

    /**
     * @Route("/getContracts/{id}", name="get_contract")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     *
     * @param int $id
     * @param Request $request
     * @param ContractMapper $mapper
     * @return View
     */
    public function getContracts(int $id, Request $request, ContractMapper $mapper) :View
    {
        $employee = $this->findEmployeeById($id);
        $contracts = $employee->getContracts();
        $json = [];
        foreach ($contracts as $contract) {
            $json[] = $mapper->mapContractToJson($contract);
        }
        return $this->view($json, Response::HTTP_OK);
    }
    /**
     * @Route("/getCSV", name="get_csv_list")
     * @Method("GET")
     */
    public function getCSVList(Exporter $exporter) :View
    {
        $token = $exporter->export();
        return $this->view(['token' => $token], Response::HTTP_OK);
    }

    /**
     * @Route("/getAll", name="get_all_employees")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Method("GET")
     */
    public function getAll(EmployeeMapperFactory $employeeMapperFactory) :View
    {
        /** @var User $user */
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        /** @var EmployeeRepository $employeeRepo */
        $employeeRepo = $em->getRepository(Employee::class);
        $employees = $employeeRepo->findAll();
        $mapper = $employeeMapperFactory->getFor($user);
        $result = array_map(
            fn(Employee $employee) : array => $mapper($employee),
            $employees,
        );
        $coll = new Collator('pl_PL');
        usort($result, function (array $a, array $b) use ($coll) {
            return $coll->compare($a['lastName'], $b['lastName']);
        });
        return $this->view($result, Response::HTTP_OK);
    }
    private function getParamsForContract(Request $request) :array
    {
        $typeId = (int)$request->get('contract_type_id');
        $employeeId = $request->get('employee_id');
        $startDate = $request->get('date_star');
        $endDate = $request->get('date_end');
        $assignDate = $request->get('date_assign');
        $noticePeriod = $request->get('notice_period');
        $active = true;
        return [
            'typeId' => $typeId,
            'employeeId' => $employeeId,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'assignDate' => $assignDate,
            'noticePeriod' => $noticePeriod,
            'active' => $active
        ];
    }
}
