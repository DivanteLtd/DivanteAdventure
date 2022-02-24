<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 14.02.19
 * Time: 10:10
 */

namespace Divante\Bundle\AdventureBundle\MessageHandler\Leaves;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\LeaveRequest;
use Divante\Bundle\AdventureBundle\Entity\LeaveRequestDay;
use Divante\Bundle\AdventureBundle\Events\RequestStatusEvent;
use Divante\Bundle\AdventureBundle\Events\SlackAdminLogEvent;
use Divante\Bundle\AdventureBundle\Message\Leaves\UpdateRequest;
use Divante\Bundle\AdventureBundle\Services\RequestMailer\TemplateFactory;
use Doctrine\ORM\EntityManagerInterface;
use Divante\Bundle\AdventureBundle\Entity\EmployeeOccupancy;
use Divante\Bundle\AdventureBundle\Entity\EmployeeWorkLocation;
use Divante\Bundle\AdventureBundle\Services\RequestMailer\Mailer;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UpdateRequestHandler
{
    private EntityManagerInterface $em;
    private TemplateFactory $template;
    private Mailer $mailer;
    private EventDispatcherInterface $dispatcher;

    public function __construct(
        EntityManagerInterface $em,
        TemplateFactory $template,
        Mailer $mailer,
        EventDispatcherInterface $dispatcher
    ) {
        $this->em = $em;
        $this->template = $template;
        $this->mailer = $mailer;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param UpdateRequest $message
     * @throws \Exception
     */
    public function __invoke(UpdateRequest $message) : void
    {
        $requestId = $message->getRequestId();
        /** @var LeaveRequest|null $request */
        $request = $this->em->getRepository(LeaveRequest::class)->find($requestId);
        if (is_null($request)) {
            throw new \Exception("Request with ID $requestId not found.");
        }
        $status = $message->getStatus() ?? $request->getStatus();
        $user = $message->getUser();
        /** @var Employee|null $employee */
        $employee = $this->em->getRepository(Employee::class)->find($user->getEmployeeId());

        if (is_null($employee) || !$request->canChangeStatus($status, $employee)) {
            throw new \Exception("User cannot change status from ".$request->getStatus()." to ".$status);
        }
        $comment = $message->getComment() ?? $request->getComment();

        $this->em->getConnection()->beginTransaction();
        $oldStatus = $request->getStatus();
        try {
            $request->setComment($comment)
                ->setStatus($status)
                ->setUpdatedAt();
            $statusChanged = $oldStatus !== $status;
            if ($statusChanged && $status === LeaveRequest::REQUEST_STATUS_ACCEPTED) {
                $request->setAcceptedAt();
            }
            $leaveRequestDays = $this->em->getRepository(LeaveRequestDay::class)
                ->findBy(['request' => $request->getId()]);
            if (empty($message->getDays()) && $oldStatus === LeaveRequest::REQUEST_STATUS_ACCEPTED
                && $request->getStatus() === LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION) {
                $this->setLeaveDayStatus($leaveRequestDays, LeaveRequestDay::DAY_STATUS_PENDING_RESIGNATION);
                if ($leaveRequestDays[0]->getType() === LeaveRequestDay::DAY_TYPE_OVERTIME) {
                    $request->setStatus(LeaveRequest::REQUEST_STATUS_RESIGNED);
                    $this->em->persist($request);
                }
            }
            if (empty($message->getDays()) && $oldStatus === LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION
                && $request->getStatus() === LeaveRequest::REQUEST_STATUS_ACCEPTED) {
                $this->setLeaveDayStatus($leaveRequestDays, LeaveRequestDay::DAY_STATUS_ACTIVE);
            }
            if (!empty($message->getDays())) {
                foreach ($leaveRequestDays as $leaveRequestDay) {
                    $days = $message->getDays();
                    if ($request->getStatus() === LeaveRequest::REQUEST_STATUS_RESIGNED
                        && $leaveRequestDay->getStatus() === LeaveRequestDay::DAY_STATUS_PENDING_RESIGNATION) {
                        $this->setSingleDayStatus($leaveRequestDay, LeaveRequestDay::DAY_STATUS_RESIGNED);
                    } elseif ($request->getStatus() === LeaveRequest::REQUEST_STATUS_ACCEPTED
                        && $leaveRequestDay->getStatus() === LeaveRequestDay::DAY_STATUS_PENDING_RESIGNATION) {
                        $this->setSingleDayStatus($leaveRequestDay, LeaveRequestDay::DAY_STATUS_ACTIVE);
                    } else {
                        $currentStatus = $request->getStatus();
                        foreach ($days as $day) {
                            if (isset($day['deleted'])) {
                                if ($leaveRequestDay->getId() === $day['id'] && $day['deleted']) {
                                    if ($request->getStatus() === LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION
                                        && $leaveRequestDay->getType() === LeaveRequestDay::DAY_TYPE_OVERTIME) {
                                        $this->setSingleDayStatus(
                                            $leaveRequestDay,
                                            LeaveRequestDay::DAY_STATUS_RESIGNED
                                        );
                                        $currentStatus = LeaveRequest::REQUEST_STATUS_ACCEPTED;
                                    } elseif ($request->getStatus()
                                        === LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION) {
                                        $this->setSingleDayStatus(
                                            $leaveRequestDay,
                                            LeaveRequestDay::DAY_STATUS_PENDING_RESIGNATION
                                        );
                                    } elseif ($request->getStatus() === LeaveRequest::REQUEST_STATUS_PENDING
                                        || $request->getStatus() === LeaveRequest::REQUEST_STATUS_PLANNED) {
                                        $this->em->remove($leaveRequestDay);
                                    }
                                }
                            }
                        }
                        if ($request->getStatus() !== $currentStatus) {
                            $request->setStatus($currentStatus);
                        }
                    }
                }
            }
            $this->em->persist($request);
            $this->em->flush();
            $this->em->getConnection()->commit();
        } catch (\Exception $e) {
            $this->em->getConnection()->rollBack();
            throw new \Exception("Updating leave request failed", 0, $e);
        }

        if ($oldStatus !== $status) {
            $newStatus = $status;
            $template = $this->template->getTemplate(
                $oldStatus,
                $newStatus,
                $request,
                $message->getUser()->getEmployee()->getCompanyBranch(),
                $message->getUser()->getEmployee()->getContractType()
            );
            $this->mailer->send($template);
        } else {
            $partialInfo = sprintf(
                'Email disappearing Case. Investigation in progress. Request %s has been changed by %s %s for %s %s.',
                $message->getRequestId(),
                $message->getUser()->getEmployee()->getLastName(),
                $message->getUser()->getEmployee()->getName(),
                $request->getEmployee()->getLastName(),
                $request->getEmployee()->getName()
            );
            $info = sprintf('%s. Additional info: Old status - %s, new status - %s', $partialInfo, $oldStatus, $status);
            $this->dispatcher->dispatch(new SlackAdminLogEvent($info));
        }

        if ($statusChanged && $message->getStatus() === LeaveRequest::REQUEST_STATUS_ACCEPTED) {
            /** @var LeaveRequestDay[] $leaveRequestDays */
            $leaveRequestDays = $this->em->getRepository(LeaveRequestDay::class)
                ->findBy(['request' => $request->getId()]);
            $this->em->getConnection()->beginTransaction();
            try {
                $employeeId = $request->getEmployee()->getId();
                foreach ($leaveRequestDays as $day) {
                    $dayDateTimestamp = $day->getDate()->getTimestamp();
                    $dayDate = $day->getDate();
                    /** @var EmployeeOccupancy|null $employeeOccupancy */
                    $employeeOccupancy = $this->em->getRepository(EmployeeOccupancy::class)
                        ->findOneBy(['employee' => $employeeId, 'date' => $dayDateTimestamp]);
                    if (!is_null($employeeOccupancy)) {
                        $employeeOccupancy->setOccupancy(0);
                        $this->em->persist($employeeOccupancy);
                    }
                    /** @var EmployeeWorkLocation|null $employeeWorkLocation */
                    $employeeWorkLocation = $this->em->getRepository(EmployeeWorkLocation::class)
                        ->findOneBy(['employeeId' => $employeeId, 'date' => $dayDate]);
                    if (!is_null($employeeWorkLocation)) {
                        $this->em->remove($employeeWorkLocation);
                    }
                    $this->em->flush();
                }
                $this->em->getConnection()->commit();
            } catch (\Exception $e) {
                $this->em->getConnection()->rollBack();
                throw new \Exception("Updating employee occupancy or work location entry failed", 0, $e);
            }
        }

        $changedByManager = $message->getUser()->getEmployeeId() === $request->getManager()->getId();
        $changedByUser = $message->getUser()->getEmployeeId() === $request->getEmployee()->getId();
        $this->dispatcher->dispatch(new RequestStatusEvent($request, $oldStatus, $changedByUser, $changedByManager));

        if (!empty($message->getDays()) && $request->getStatus() === LeaveRequest::REQUEST_STATUS_RESIGNED) {
            $this->em->getConnection()->beginTransaction();
            $days = $message->getDays();
            foreach ($days as $day) {
                if ($day['status'] === LeaveRequestDay::DAY_STATUS_ACTIVE) {
                    $request->setStatus(LeaveRequest::REQUEST_STATUS_ACCEPTED);
                    $this->em->persist($request);
                    continue;
                }
            }
            $this->em->flush();
            $this->em->getConnection()->commit();
        }
    }

    private function setLeaveDayStatus($leaveRequestDays, $status)
    {
        /** @var LeaveRequestDay $leaveRequestDay */
        foreach ($leaveRequestDays as $leaveRequestDay) {
            if ($leaveRequestDay->getType() === LeaveRequestDay::DAY_TYPE_OVERTIME) {
                $leaveRequestDay->setStatus(LeaveRequestDay::DAY_STATUS_RESIGNED);
                $this->em->persist($leaveRequestDay);
            }
            if ($leaveRequestDay->getStatus() !== LeaveRequestDay::DAY_STATUS_RESIGNED) {
                $leaveRequestDay->setStatus($status);
                $this->em->persist($leaveRequestDay);
            }
        }
    }

    private function setSingleDayStatus($leaveRequestDay, $status)
    {
        /** @var LeaveRequestDay $leaveRequestDay */
        $leaveRequestDay->setStatus($status);
        $this->em->persist($leaveRequestDay);
    }
}
