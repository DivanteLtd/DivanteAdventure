<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 13.02.19
 * Time: 13:54
 */

namespace Divante\Bundle\AdventureBundle\MessageHandler\Leaves;

use Divante\Bundle\AdventureBundle\Entity\EmployeeOccupancy;
use Divante\Bundle\AdventureBundle\Entity\EmployeeWorkLocation;
use Divante\Bundle\AdventureBundle\Entity\LeaveRequest;
use Divante\Bundle\AdventureBundle\Entity\LeaveRequestDay;
use Divante\Bundle\AdventureBundle\Events\RequestEvent;
use Divante\Bundle\AdventureBundle\Message\Leaves\CreateRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use DateTime;

class CreateRequestHandler
{
    private EntityManagerInterface $em;
    private EventDispatcherInterface $dispatcher;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $dispatcher)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param CreateRequest $message
     * @throws \Exception
     */
    public function __invoke(CreateRequest $message) : void
    {
        $this->em->getConnection()->beginTransaction();
        $request = new LeaveRequest();
        try {
            $request
                ->setStatus($message->getStatus() === 0
                    ? LeaveRequest::REQUEST_STATUS_PENDING : LeaveRequest::REQUEST_STATUS_PLANNED)
                ->setPeriod($message->getPeriod())
                ->setManager($message->getManager())
                ->setComment($message->getComment())
                ->setCreatedAt()
                ->setAcceptedAt(null)
                ->setUpdatedAt();

            foreach ($message->getAttachments() as $attachment) {
                $request->addAttachment($attachment);
            }
            $this->em->persist($request);

            foreach ($message->getDaysData() as $dayData) {
                $day = new LeaveRequestDay();
                $day->setDate(DateTime::createFromFormat('Y-m-d', $dayData['date']))
                    ->setType($dayData['type'])
                    ->setHours($dayData['hour'])
                    ->setStatus(LeaveRequestDay::DAY_STATUS_ACTIVE)
                    ->setRequest($request);
                if (!$day->requiresAcceptance() && $message->getStatus() === 0) {
                    $request->setStatus(LeaveRequest::REQUEST_STATUS_ACCEPTED);
                }
                $request->getRequestDays()->add($day);
                $this->em->persist($day);
            }
            $this->em->flush();
            $this->em->getConnection()->commit();
            if ($message->getDaysData()[0]['type'] === LeaveRequestDay::DAY_TYPE_OVERTIME) {
                $this->em->getConnection()->beginTransaction();
                foreach ($message->getDaysData() as $day) {
                    $dayDate = new \DateTime($day['date']);
                    $dayDateTimestamp = (new \DateTime($day['date']))->setTime(0, 0, 0)->getTimestamp();
                    /** @var EmployeeOccupancy|null $employeeOccupancy */
                    $employeeOccupancy = $this->em->getRepository(EmployeeOccupancy::class)
                        ->findOneBy(['employee' => $message->getEmployeeId(), 'date' => $dayDateTimestamp]);
                    if (!is_null($employeeOccupancy)) {
                        $employeeOccupancy->setOccupancy(0);
                        $this->em->persist($employeeOccupancy);
                    }
                    /** @var EmployeeWorkLocation|null $employeeWorkLocation */
                    $employeeWorkLocation = $this->em->getRepository(EmployeeWorkLocation::class)
                        ->findOneBy(['employeeId' => $message->getEmployeeId(), 'date' => $dayDate]);
                    if (!is_null($employeeWorkLocation)) {
                        $this->em->remove($employeeWorkLocation);
                    }
                    $this->em->flush();
                }
                $this->em->getConnection()->commit();
            }
        } catch (\Exception $e) {
            $this->em->getConnection()->rollBack();
            throw new \Exception("Creating leave request failed", 0, $e);
        }

        $event = new RequestEvent($request);
        $this->dispatcher->dispatch($event);
    }
}
