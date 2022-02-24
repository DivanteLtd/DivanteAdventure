<?php

namespace Divante\Bundle\AdventureBundle\MessageHandler\Employee;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\EmployeeWorkLocation;
use Divante\Bundle\AdventureBundle\Entity\Repository\WorkLocation\WorkLocationRepository;
use Divante\Bundle\AdventureBundle\Events\WorkLocationStatusEvent;
use Divante\Bundle\AdventureBundle\Message\Employee\CreateEmployeeWorkLocation;
use Divante\Bundle\AdventureBundle\Representation\Email\EmailSenderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Translation\TranslatorInterface;

class CreateEmployeeWorkLocationHandler
{
    private EntityManagerInterface $em;
    private EventDispatcherInterface $eventDispatcher;
    private EmailSenderInterface $mailer;
    private TranslatorInterface $translator;

    public function __construct(
        EntityManagerInterface $em,
        EventDispatcherInterface $eventDispatcher,
        EmailSenderInterface $mailer,
        TranslatorInterface $translator
    ) {
        $this->em = $em;
        $this->eventDispatcher = $eventDispatcher;
        $this->mailer = $mailer;
        $this->translator = $translator;
    }

    /**
     * @param CreateEmployeeWorkLocation $message
     * @throws \Exception
     */
    public function __invoke(CreateEmployeeWorkLocation $message) : void
    {
        $userId = $message->getUserId();
        $type = $message->getType();
        $dates = $message->getDates();
        $em = $this->em;
        $em->beginTransaction();
        /** @var WorkLocationRepository $employeeWorkLocationsRepo */
        $employeeWorkLocationsRepo = $em->getRepository(EmployeeWorkLocation::class);
        $employeeWorkLocations = $employeeWorkLocationsRepo->queryEmployeeDates($userId, $type);
        $today = (new \DateTime())->format('Y-m-d');
        $slackNotification = true;
        if ($employeeWorkLocations) {
            foreach ($employeeWorkLocations as $employeeWorkLocation) {
                if ($employeeWorkLocation->getDate()->format('Y-m-d') === $today) {
                    $slackNotification = false;
                }
                if ($employeeWorkLocation->getDate()->format('Y-m-d') >= $today) {
                    $em->remove($employeeWorkLocation);
                }
            }
            $em->flush();
        }
        /** @var Employee $employee */
        $employee = $em->getRepository(Employee::class)->find($userId);
        try {
            foreach ($dates as $date) {
                $date = new \DateTime($date);
                $date = $date->setTime(23, 59, 59);
                $workLocation = (new EmployeeWorkLocation())
                    ->setEmployeeId($userId)
                    ->setType($type)
                    ->setDate($date);
                $em->persist($workLocation);
                if ($date->format('Y-m-d') === $today && $slackNotification) {
                    $this->eventDispatcher->dispatch(new WorkLocationStatusEvent($workLocation, $employee));
                }
            }
            $em->flush();
            $em->commit();
        } catch (\Exception $e) {
            $em->rollback();
            throw new \Exception($e);
        }
    }
}
