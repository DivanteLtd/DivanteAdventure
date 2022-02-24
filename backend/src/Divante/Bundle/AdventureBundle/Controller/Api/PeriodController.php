<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 13.02.19
 * Time: 10:21
 */

namespace Divante\Bundle\AdventureBundle\Controller\Api;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\LeavePeriod;
use Divante\Bundle\AdventureBundle\Mappers\PeriodMapper;
use Divante\Bundle\AdventureBundle\Message\Leaves\CreatePeriod;
use Divante\Bundle\AdventureBundle\Message\Leaves\DeleteLeavePeriod;
use Divante\Bundle\AdventureBundle\Message\Leaves\UpdatePeriod;
use Divante\Bundle\AdventureBundle\Query\FreeDays\FreeDaysReportQuery;
use Divante\Bundle\AdventureBundle\Entity\User;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Class PeriodController
 * @package Divante\Bundle\AdventureBundle\Controller\Api
 * @Route("api/period")
 */
class PeriodController extends FOSRestController
{
    /**
     * @Route("", name="period_create")
     * @Method("POST")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param Request $request
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function createNewPeriod(Request $request, MessageBusInterface $messageBus) : View
    {
        $message = new CreatePeriod(
            $request->get('dateFrom') ?? '1970-01-01',
            $request->get('dateTo') ??'1970-01-01',
            $request->get('sickLeaveDays') ?? 0,
            $request->get('freeDays') ?? 0,
            $request->get('employeeId') ?? -1
        );
        try {
            $messageBus->dispatch($message);
            return $this->view([], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("", name="period_get")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     * @param EntityManagerInterface $em
     * @param PeriodMapper $mapper
     * @return View
     */
    public function getUserPeriods(EntityManagerInterface $em, PeriodMapper $mapper) : View
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var Employee $employee */
        $employee = $em->getRepository(Employee::class)->find($user->getEmployeeId());
        return $this->loadPeriodsFromDatabase($employee, $em, $mapper);
    }

    /**
     * @Route("/report", name="period_get_free_days_report")
     * @Method("GET")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param FreeDaysReportQuery $query
     * @return View
     */
    public function getFreeDaysReport(FreeDaysReportQuery $query) : View
    {
        try {
            return $this->view($query->getReport(), Response::HTTP_OK);
        } catch (Exception $exception) {
            return $this->view($exception, Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/{id}", name="period_get_by_employee")
     * @Method("GET")
     * @Security("has_role('ROLE_MANAGER')")
     * @param int $id
     * @param EntityManagerInterface $em
     * @param PeriodMapper $mapper
     * @return View
     */
    public function getPeriodsByEmployee(int $id, EntityManagerInterface $em, PeriodMapper $mapper) : View
    {
        /** @var Employee|null $employee */
        $employee = $em->getRepository(Employee::class)->find($id);
        if (is_null($employee)) {
            return $this->view([], Response::HTTP_NOT_FOUND);
        }
        return $this->loadPeriodsFromDatabase($employee, $em, $mapper);
    }

    private function loadPeriodsFromDatabase(
        Employee $employee,
        EntityManagerInterface $em,
        PeriodMapper $mapper
    ) : View {
        $periodsRepo = $em->getRepository(LeavePeriod::class);
        /** @var LeavePeriod[] $periods */
        $periods = $periodsRepo->findBy(['employee' => $employee], ['id' => Criteria::ASC]);
        $result = array_map($mapper, $periods);
        return $this->view($result, Response::HTTP_OK);
    }

    /**
     * @Route("/{id}", name="period_update")
     * @Method("PATCH")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @param int $id
     * @param Request $request
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function updatePeriod(int $id, Request $request, MessageBusInterface $messageBus) : View
    {
        $message = new UpdatePeriod(
            $id,
            $request->get('dateFrom'),
            $request->get('dateTo'),
            $request->get('sickLeaveDays') ?? -1,
            $request->get('freeDays') ?? -1,
            $request->get('employeeId') ?? -1,
            $request->get('comment') ?? ''
        );
        try {
            $messageBus->dispatch($message);
            return $this->view([], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Delete period
     *
     * @Route("/{id}", name="period_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     *
     * @param int $id
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function deleteAction(int $id, MessageBusInterface $messageBus): View
    {
        $message = new DeleteLeavePeriod($id);
        try {
            $messageBus->dispatch($message);
            return $this->view([], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
