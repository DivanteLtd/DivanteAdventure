<?php
/**
 * Created by PhpStorm.
 * User: norbert
 * Date: 21.12.18
 * Time: 10:43
 */

namespace Divante\Bundle\AdventureBundle\Controller\Api;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\LeaveRequest;
use Divante\Bundle\AdventureBundle\Entity\LeaveRequestDay;
use Divante\Bundle\AdventureBundle\Entity\Repository\EmployeeRepository;
use Divante\Bundle\AdventureBundle\Entity\Repository\LeaveRequestDayRepository;
use Divante\Bundle\AdventureBundle\Entity\Tribe;
use Divante\Bundle\AdventureBundle\Infrastructure\Config\SystemConfig;
use Divante\Bundle\AdventureBundle\Mappers\Employee\EmployeeMapperFactory;
use Divante\Bundle\AdventureBundle\Mappers\LeaveDayMapper;
use Divante\Bundle\AdventureBundle\Entity\User;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use DateTime;

/**
 * Class LeaveDaysController
 * @package Divante\Bundle\AdventureBundle\Controller\Api
 * @Route("api/leave")
 */
class LeaveDaysController extends FOSRestController
{
    private LeaveDayMapper $leaveDayMapper;
    private EmployeeMapperFactory $employeeMapperFactory;

    public function __construct(LeaveDayMapper $leaveDayMapper, EmployeeMapperFactory $employeeMapperFactory)
    {
        $this->leaveDayMapper = $leaveDayMapper;
        $this->employeeMapperFactory = $employeeMapperFactory;
    }

    /**
     * Lists all leave days.
     *
     * Access: ADMIN, USER
     *
     * @Route("", name="leave_index")
     * @Method("GET")
     *
     * @Security("has_role('ROLE_MANAGER')")
     *
     * @param Request $request
     * @return View
     */
    public function indexAction(Request $request) : View
    {
        $start = $request->query->get('start', -1);
        $end = $request->query->get('end', -1);
        $startDate = \Datetime::createFromFormat("Y-m-d", $start);
        $endDate = \Datetime::createFromFormat("Y-m-d", $end);
        $em = $this->getDoctrine()->getManager();
        /** @var LeaveRequestDayRepository $repoLeaveDays */
        $repoLeaveDays = $em->getRepository(LeaveRequestDay::class);
        $returnArray = [];

        $acceptedRequestStatus = [
            LeaveRequest::REQUEST_STATUS_ACCEPTED,
            LeaveRequest::REQUEST_STATUS_PENDING
        ];

        $requestDays = $repoLeaveDays->getLeaveRequestDays($startDate, $endDate);

        /** @var LeaveRequestDay $leaveDay */
        foreach ($requestDays as $leaveDay) {
            $dayStatus = $leaveDay->getStatus();
            $requestStatus = $leaveDay->getRequest()->getStatus();
            if ($dayStatus === LeaveRequestDay::DAY_STATUS_ACTIVE && in_array($requestStatus, $acceptedRequestStatus)) {
                $returnArray[] = $this->leaveDayMapper->mapEntity($leaveDay);
            }
        }

        return $this->view($returnArray, Response::HTTP_OK);
    }

    /**
     * Lists leave days for summary. It should - if user is not manager - filter out people who are not in the same
     * tribe
     * @Route("/summary", name="leave_summary")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param SystemConfig $config
     * @return View
     */
    public function freeDaysSummary(Request $request, SystemConfig $config) : View
    {
        $start = $request->query->get('start', -1);
        $end = $request->query->get('end', -1);
        $startDate = \Datetime::createFromFormat("Y-m-d", $start);
        $endDate = \Datetime::createFromFormat("Y-m-d", $end);

        $em = $this->getDoctrine()->getManager();
        /** @var EmployeeRepository $employeeRepo */
        $employeeRepo = $em->getRepository(Employee::class);
        /** @var User $user */
        $user = $this->getUser();
        /** @var Employee $currentEmployee */
        $currentEmployee = $employeeRepo->find($user->getEmployeeId());
        $tribe = $currentEmployee->getTribe();

        $isManager = $user->hasRole('ROLE_HR')
            || $user->hasRole('ROLE_MANAGER')
            || $user->hasRole('ROLE_TRIBE_MASTER')
            || $user->hasRole('ROLE_SUPER_ADMIN');
        if ($isManager) {
            $employees = $employeeRepo->findAllWithoutFormerEmployees();
            return $this->createView($employees, $startDate, $endDate, false);
        } elseif (is_null($tribe)) {
            return $this->createView([$currentEmployee], $startDate, $endDate, true);
        } else {
            $connectedTribe1ID = $config->getValueOrDefault(SystemConfig::KEY_TRIBE_CONNECTION_1_ID, '');
            $connectedTribe2ID = $config->getValueOrDefault(SystemConfig::KEY_TRIBE_CONNECTION_2_ID, '');
            $employees = $this->filterEmployees($tribe);
            if ($tribe->getId() === (int)$connectedTribe1ID) {
                $connectedTribe2 = $em->getRepository(Tribe::class)->find((int)$connectedTribe2ID);
                $connectedTribe2Employees =  $this->filterEmployees($connectedTribe2);
                $employees = array_merge($employees, $connectedTribe2Employees);
            } elseif ($tribe->getId() === (int)$connectedTribe2ID) {
                $connectedTribe1 = $em->getRepository(Tribe::class)->find((int)$connectedTribe1ID);
                $connectedTribe1Employees = $this->filterEmployees($connectedTribe1);
                $employees = array_merge($employees, $connectedTribe1Employees);
            }
            return $this->createView($employees, $startDate, $endDate, true);
        }
    }

    private function filterEmployees(Tribe $tribe) : array
    {
        return array_filter(
            $tribe->getEmployees()->toArray(),
            function (Employee $employee) {
                return !$employee->isFormer();
            }
        );
    }

    /**
     * @param Employee[] $employees
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param bool $hideData
     * @return View
     */
    private function createView(array $employees, \DateTime $startDate, \DateTime $endDate, bool $hideData) : View
    {
        if ($hideData) {
            $acceptedTypes = [
                LeaveRequest::REQUEST_STATUS_ACCEPTED,
                LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION
            ];
        } else {
            $acceptedTypes = [
                LeaveRequest::REQUEST_STATUS_ACCEPTED,
                LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION,
                LeaveRequest::REQUEST_STATUS_PENDING,
                LeaveRequest::REQUEST_STATUS_PLANNED,
            ];
        }

        /** @var LeaveRequestDayRepository $leaveRepository */
        $leaveRepository = $this->getDoctrine()->getRepository(LeaveRequestDay::class);
        $result = [];
        /** @var User $user */
        $user = $this->getUser();
        $employeeMapper = $this->employeeMapperFactory->getFor($user);
        /** @var Employee $employee */
        foreach ($employees as $employee) {
            $entry = [
                "employee" => $employeeMapper($employee),
                "freeDays" => []
            ];
            $requestDays = $leaveRepository->getLeaveRequestDays($startDate, $endDate, $employee);
            /** @var LeaveRequestDay $leaveDay */
            foreach ($requestDays as $leaveDay) {
                if ($leaveDay->getStatus() !== LeaveRequestDay::DAY_STATUS_RESIGNED) {
                    if (!in_array($leaveDay->getRequest()->getStatus(), $acceptedTypes)) {
                        continue;
                    }
                    if ($hideData) {
                        $leaveDay->setType(-1);
                    }
                    $entry['freeDays'][] = $this->leaveDayMapper->mapEntity($leaveDay);
                }
            }
            $result[] = $entry;
        }
        return $this->view($result, Response::HTTP_OK);
    }

    /**
     * Lists confirmed and >= today leave days by employee
     *
     * @Route("/confirmed", name="leave_confirmed")
     * @Method("GET")
     * @return View
     */
    public function confirmedEmployeeLeaveDays() : View
    {
        $start = (new \DateTime());
        $end = (new \DateTime('+3 months'));
        $userId = $this->getUser()->getEmployeeId();

        $em = $this->getDoctrine()->getManager();
        /** @var LeaveRequestDayRepository $repoLeaveDays */
        $repoLeaveDays = $em->getRepository(LeaveRequestDay::class);

        $acceptedRequestStatus = [
            LeaveRequest::REQUEST_STATUS_ACCEPTED,
            LeaveRequest::REQUEST_STATUS_PENDING
        ];
        /** @var Employee $employee */
        $employee = $em->getRepository(Employee::class)->find($userId);
        $requestDays = $repoLeaveDays->getLeaveRequestDays($start, $end, $employee);

        $returnArray = [];
        /** @var LeaveRequestDay $leaveDay */
        foreach ($requestDays as $leaveDay) {
            $dayStatus = $leaveDay->getStatus();
            $requestStatus = $leaveDay->getRequest()->getStatus();
            if ($dayStatus === LeaveRequestDay::DAY_STATUS_ACTIVE && in_array($requestStatus, $acceptedRequestStatus)) {
                $returnArray[] = $this->leaveDayMapper->mapEntity($leaveDay);
            }
        }

        return $this->view($returnArray, Response::HTTP_OK);
    }
}
