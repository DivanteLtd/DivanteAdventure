<?php

namespace Divante\Bundle\AdventureBundle\EventSubscriber\ProjectAssignment;

use DateTime;
use Divante\Bundle\AdventureBundle\Entity\DataProcessingHistory;
use Divante\Bundle\AdventureBundle\Events\ProjectAssignEvent;
use Divante\Bundle\AdventureBundle\Representation\Email\EmailConfiguration;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ProjectAssignedSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;
    private EmailConfiguration $email;
    private TranslatorInterface $translator;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        EmailConfiguration $email,
        TranslatorInterface $translator
    ) {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->email = $email;
        $this->translator = $translator;
    }

    /** @return array<string,array<int,array<int,string|int>>> */
    public static function getSubscribedEvents(): array
    {
        return [
            ProjectAssignEvent::class => [
                [ 'addDataProcessingHistory', 0 ],
                [ 'sendNotification', 10 ],
            ],
        ];
    }

    public function sendNotification(ProjectAssignEvent $event): void
    {
        try {
            $project = $event->getProject();
            $employee = $event->getEmployee();
            $language = $employee->getLanguage();
            $this->translator->setLocale($language);
            $subject = $this->translator->trans('informationAboutDataProcessingInTheProject');
            $this->email->sendMessage(
                $employee->getEmail(),
                null,
                sprintf('%s %s', $subject, $project->getName()),
                [
                    'projectName' => $project->getName(),
                    'date'        => date("d-m-Y"),
                    'criteria'    => $project->getCriteria()->toArray(),
                    'isAssigned'  => true
                ],
                'AdventureBundle:Emails:employee_project_assignment_data_processing.html.twig'
            );
        } catch (Exception $ex) {
            $this->logger->debug($ex->getMessage());
        }
    }

    public function addDataProcessingHistory(ProjectAssignEvent $event): void
    {
        try {
            $employee = $event->getEmployee();
            $history = new DataProcessingHistory();
            $history->setFirstName($employee->getName())
                ->setLastName($employee->getLastName())
                ->setProject($event->getProject())
                ->setStartedAt(new DateTime());
            $this->entityManager->persist($history);
            $this->entityManager->flush();
        } catch (Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }
}
