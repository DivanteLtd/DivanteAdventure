<?php

namespace Divante\Bundle\AdventureBundle\EventSubscriber;

use Divante\Bundle\AdventureBundle\Entity\LeaveRequest;
use Divante\Bundle\AdventureBundle\Entity\LeaveRequestDay;
use Divante\Bundle\AdventureBundle\Events\RequestEvent;
use Divante\Bundle\AdventureBundle\Representation\Email\EmailConfiguration;
use Divante\Bundle\AdventureBundle\Services\Slack\Api\SlackSender;
use Divante\Bundle\AdventureBundle\Services\Slack\MessageTemplates\NewRequestMessage;
use Divante\Bundle\AdventureBundle\Services\Slack\SlackSendableMessage;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\TranslatorInterface;

class LeaveRequestSubscriber implements EventSubscriberInterface
{
    private EmailConfiguration $emailConfiguration;
    private NewRequestMessage $messageTemplate;
    private SlackSender $slackSender;
    protected TranslatorInterface $translator;

    public function __construct(
        EmailConfiguration $emailConfiguration,
        NewRequestMessage $messageTemplate,
        SlackSender $sender,
        TranslatorInterface $translator
    ) {
        $this->emailConfiguration = $emailConfiguration;
        $this->messageTemplate = $messageTemplate;
        $this->slackSender = $sender;
        $this->translator = $translator;
    }

    /** @return array<string,array<int,array<int,string|int>>> */
    public static function getSubscribedEvents() : array
    {
        return [
            RequestEvent::class => [
                [ 'sendMail', 0 ],
                [ 'sendSlackMessage', 0 ],
            ],
        ];
    }

    public function sendMail(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $employee = $request->getEmployee();
        $language = $request->getManager()->getLanguage();
        $this->translator->setLocale($language);
        $subject = sprintf(
            '[#%d] %s %s - %s',
            $request->getId(),
            $employee->getName(),
            $employee->getLastName(),
            $this->getAction($request),
        );

        $this->emailConfiguration->sendMessage(
            $request->getManager()->getEmail(),
            null,
            $subject,
            [
                'request' => $request,
            ],
            'AdventureBundle:Emails:leave_request_new.html.twig'
        );
    }

    public function sendSlackMessage(RequestEvent $event): void
    {
        $request = $event->getRequest();
        /** @var SlackSendableMessage $message */
        $message = $this->messageTemplate->setRequest($request)->getMessage($request->getManager());
        $this->slackSender->send($message, $request->getManager());
    }

    private function getAction(LeaveRequest $request) : string
    {
        /** @var LeaveRequestDay $firstDay */
        $firstDay = $request->getRequestDays()->get(0);
        $status = $request->getStatus();
        $type = $firstDay->getType();
        if ($status === LeaveRequest::REQUEST_STATUS_PENDING) {
            return $this->translator->trans('daysOffRequestHasBeenSubmitted');
        } elseif ($status === LeaveRequest::REQUEST_STATUS_PLANNED) {
            return $this->translator->trans('daysOffRequestHasBeenPlanned');
        } elseif ($status === LeaveRequest::REQUEST_STATUS_ACCEPTED && $type === LeaveRequestDay::DAY_TYPE_OVERTIME) {
            return $this->translator->trans('freeDaysAfterOvertime');
        } elseif ($status === LeaveRequest::REQUEST_STATUS_ACCEPTED
            && $type === LeaveRequestDay::DAY_TYPE_ADDITIONAL_HOURS) {
            return $this->translator->trans('freeDaysAfterAdditionalHours');
        } else {
            throw new Exception("Unrecognized situation");
        }
    }
}
