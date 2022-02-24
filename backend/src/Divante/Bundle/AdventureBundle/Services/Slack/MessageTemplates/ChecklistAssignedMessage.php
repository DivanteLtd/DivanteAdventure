<?php

namespace Divante\Bundle\AdventureBundle\Services\Slack\MessageTemplates;

use Divante\Bundle\AdventureBundle\Entity\Checklist\Checklist;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\EventSubscriber\Checklist\ChecklistNotificationEntry;
use Divante\Bundle\AdventureBundle\Services\Slack\Api\SlackSender;
use Divante\Bundle\AdventureBundle\Services\Slack\SlackMessage;
use Symfony\Component\Translation\TranslatorInterface;

class ChecklistAssignedMessage
{
    private TranslatorInterface $translator;
    private SlackSender $sender;
    private ?Checklist $checklist = null;
    private ?ChecklistNotificationEntry $entry = null;

    public function __construct(TranslatorInterface $translator, SlackSender $sender)
    {
        $this->translator = $translator;
        $this->sender = $sender;
    }

    public function setChecklist(Checklist $checklist): self
    {
        $this->checklist = $checklist;
        return $this;
    }

    public function setNotificationEntry(ChecklistNotificationEntry $entry): self
    {
        $this->entry = $entry;
        return $this;
    }

    public function sendMessage(): void
    {
        if (is_null($this->entry)) {
            return;
        }
        $receiver = $this->entry->getEmployee();
        $this->translator->setLocale($receiver->getSlackLanguage());
        $messageTemplate = $this->getTranslation($receiver->getGender() ?? -1);
        $checklistName = $this->getChecklistNameFor($receiver);
        if (empty($messageTemplate) || empty($checklistName)) {
            return;
        }
        $message = sprintf($messageTemplate, $checklistName);
        $this->sender->send(new SlackMessage($message), $receiver);
    }

    private function getChecklistNameFor(Employee $receiver): string
    {
        if (is_null($this->checklist)) {
            return '';
        }
        if (strtoupper($receiver->getSlackLanguage()) === 'PL') {
            return $this->checklist->getNamePl();
        }
        return $this->checklist->getNameEn();
    }

    private function getTranslation(int $gender): string
    {
        if (is_null($this->entry)) {
            return '';
        }
        $subject = $this->entry->hasType(ChecklistNotificationEntry::NOTIFICATION_SUBJECT);
        $owner = $this->entry->hasType(ChecklistNotificationEntry::NOTIFICATION_OWNER);
        $responsible = $this->entry->hasType(ChecklistNotificationEntry::NOTIFICATION_TASK_RESPONSIBLE);
        if ($subject && $owner && $responsible) {
            return $this->translator->transChoice('slack.checklist_assign.subject_owner_responsible', $gender);
        }
        if ($subject && $owner) {
            return $this->translator->transChoice('slack.checklist_assign.subject_owner', $gender);
        }
        if ($subject && $responsible) {
            return $this->translator->transChoice('slack.checklist_assign.subject_responsible', $gender);
        }
        if ($owner && $responsible) {
            return $this->translator->transChoice('slack.checklist_assign.owner_responsible', $gender);
        }
        if ($subject) {
            return $this->translator->transChoice('slack.checklist_assign.subject', $gender);
        }
        if ($owner) {
            return $this->translator->transChoice('slack.checklist_assign.owner', $gender);
        }
        if ($responsible) {
            return $this->translator->transChoice('slack.checklist_assign.responsible', $gender);
        }
        return '';
    }
}
