<?php

namespace Divante\Bundle\AdventureBundle\EventSubscriber\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Checklist\Checklist;
use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistQuestion;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Events\Checklist\ChecklistAssignedEvent;
use Divante\Bundle\AdventureBundle\Events\SlackAdminLogEvent;
use Divante\Bundle\AdventureBundle\Services\Slack\MessageTemplates\ChecklistAssignedMessage;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ChecklistAssignedSubscriber implements EventSubscriberInterface
{
    private ChecklistAssignedMessage $slackMessage;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(ChecklistAssignedMessage $slackMessage, EventDispatcherInterface $eventDispatcher)
    {
        $this->slackMessage = $slackMessage;
        $this->eventDispatcher = $eventDispatcher;
    }

    /** @return array<string,array<int,array<int,string|int>>> */
    public static function getSubscribedEvents()
    {
        return [
            ChecklistAssignedEvent::class => [
                [ 'sendNotifications', 0 ],
                [ 'dispatchSlackLogEvent', 10 ],
            ],
        ];
    }

    public function dispatchSlackLogEvent(ChecklistAssignedEvent $event): void
    {
        $message = sprintf(
            "*%s %s* has assigned a new checklist \"*%s*\"; subject: %s %s",
            $event->getUser()->getName(),
            $event->getUser()->getLastName(),
            $event->getChecklist()->getNameEn(),
            $event->getChecklist()->getSubject()->getName(),
            $event->getChecklist()->getSubject()->getLastName(),
        );
        $this->eventDispatcher->dispatch(new SlackAdminLogEvent($message));
    }

    public function sendNotifications(ChecklistAssignedEvent $event): void
    {
        $checklist = $event->getChecklist();
        $notifications = $this->prepareNotificationsList($checklist);
        $this->slackMessage->setChecklist($checklist);
        foreach ($notifications->getEmployees() as $employee) {
            $this->slackMessage->setNotificationEntry($notifications->getForEmployee($employee))->sendMessage();
        }
    }

    private function prepareNotificationsList(Checklist $checklist): ChecklistNotificationsCollection
    {
        $notifications = new ChecklistNotificationsCollection();
        if (!$checklist->isHidden()) {
            $notifications->addNotification(
                $checklist->getSubject(),
                ChecklistNotificationEntry::NOTIFICATION_SUBJECT
            );
        }
        /** @var Employee $owner */
        foreach ($checklist->getOwners() as $owner) {
            $notifications->addNotification($owner, ChecklistNotificationEntry::NOTIFICATION_OWNER);
        }
        if ($checklist->getType() === Checklist::TYPE_DISTRIBUTED) {
            /** @var ChecklistQuestion $question */
            foreach ($checklist->getQuestions() as $question) {
                /** @var Employee $responsible */
                $responsible = $question->getResponsible();
                $notifications->addNotification(
                    $responsible,
                    ChecklistNotificationEntry::NOTIFICATION_TASK_RESPONSIBLE
                );
            }
        }
        return $notifications;
    }
}
