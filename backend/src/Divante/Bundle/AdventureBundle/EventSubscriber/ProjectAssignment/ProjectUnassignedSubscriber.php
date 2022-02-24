<?php

namespace Divante\Bundle\AdventureBundle\EventSubscriber\ProjectAssignment;

use DateTime;
use Divante\Bundle\AdventureBundle\Entity\DataProcessingHistory;
use Divante\Bundle\AdventureBundle\Events\ProjectUnassignEvent;
use Divante\Bundle\AdventureBundle\Representation\Email\EmailConfiguration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ProjectUnassignedSubscriber implements EventSubscriberInterface
{
    private ObjectRepository $historyRepository;
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;
    private EmailConfiguration $email;
    protected TranslatorInterface $translator;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        EmailConfiguration $email,
        TranslatorInterface $translator
    ) {
        $this->translator = $translator;
        $this->email = $email;
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->historyRepository = $entityManager->getRepository(DataProcessingHistory::class);
    }

    /** @return array<string,array<int,array<int,string|int>>> */
    public static function getSubscribedEvents(): array
    {
        return [
            ProjectUnassignEvent::class => [
                [ 'addDataProcessingHistory', 0 ],
                [ 'sendNotification', 10 ],
            ],
        ];
    }

    public function addDataProcessingHistory(ProjectUnassignEvent $event): void
    {
        try {
            $history = $this->historyRepository->findBy([
                'project' => $event->getProject()->getId(),
                'endedAt' => null,
            ]);

            /** @var DataProcessingHistory $entry */
            foreach ($history as $entry) {
                $entry->setEndedAt(new DateTime());
                $this->entityManager->merge($entry);
            }

            $this->entityManager->flush();
        } catch (Exception $ex) {
            $this->logger->debug($ex->getMessage());
        }
    }

    public function sendNotification(ProjectUnassignEvent $event): void
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
                    'isAssigned'  => false
                ],
                'AdventureBundle:Emails:employee_project_assignment_data_processing.html.twig'
            );
        } catch (Exception $ex) {
            $this->logger->debug($ex->getMessage());
        }
    }
}
