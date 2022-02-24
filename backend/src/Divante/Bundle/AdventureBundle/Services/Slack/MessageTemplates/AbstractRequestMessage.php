<?php

namespace Divante\Bundle\AdventureBundle\Services\Slack\MessageTemplates;

use Divante\Bundle\AdventureBundle\Entity\LeaveRequest;
use Divante\Bundle\AdventureBundle\Entity\LeaveRequestDay;
use Divante\Bundle\AdventureBundle\Infrastructure\Config\FrontendUrlSupplier;
use Divante\Bundle\AdventureBundle\Services\Slack\Api\SlackReceiver;
use Divante\Bundle\AdventureBundle\Services\Slack\SlackActionBlock;
use Divante\Bundle\AdventureBundle\Services\Slack\SlackContextBlock;
use Divante\Bundle\AdventureBundle\Services\Slack\SlackMessage;
use Divante\Bundle\AdventureBundle\Services\Slack\SlackSectionBlock;
use Divante\Bundle\AdventureBundle\Services\Slack\SlackSendableMessage;
use Divante\Bundle\AdventureBundle\Services\Slack\SlackUrlButton;
use Symfony\Component\Translation\TranslatorInterface;

abstract class AbstractRequestMessage
{
    private const MONTHS_STRING = [
        'slack.month_short.jan' => 'Jan',
        'slack.month_short.feb' => 'Feb',
        'slack.month_short.mar' => 'Mar',
        'slack.month_short.apr' => 'Apr',
        'slack.month_short.may' => 'May',
        'slack.month_short.jun' => 'Jun',
        'slack.month_short.jul' => 'Jul',
        'slack.month_short.aug' => 'Aug',
        'slack.month_short.sep' => 'Sep',
        'slack.month_short.oct' => 'Oct',
        'slack.month_short.nov' => 'Nov',
        'slack.month_short.dec' => 'Dec',
    ];

    private LeaveRequest $request;
    private string $frontendUrl;
    private TranslatorInterface $translator;

    public function __construct(FrontendUrlSupplier $frontendUrlSupplier, TranslatorInterface $translator)
    {
        $this->frontendUrl = $frontendUrlSupplier->getFrontendUrl();
        $this->translator = $translator;
    }

    public function setRequest(LeaveRequest $request) : self
    {
        $this->request = $request;
        return $this;
    }

    protected function getRequest() : LeaveRequest
    {
        return $this->request;
    }

    public function getMessage(SlackReceiver $receiver): SlackSendableMessage
    {
        $this->translator->setLocale($receiver->getSlackLanguage());
        return new SlackMessage($this->getNotificationMessage($this->request), [
            new SlackSectionBlock($this->createMessage($this->request)),
            $this->buildRequestInfoBlock(),
            $this->addLinkToRequests($this->request) ? $this->buildLinkToRequestsBlock() : null,
        ]);
    }

    protected function getTranslator() : TranslatorInterface
    {
        return $this->translator;
    }

    abstract protected function getNotificationMessage(LeaveRequest $request) : string;
    abstract protected function createMessage(LeaveRequest $request) : string;
    abstract protected function addLinkToRequests(LeaveRequest $request) : bool;

    private function buildLinkToRequestsBlock() : SlackSendableMessage
    {
        $requestsUrl = sprintf("%s/#/requests", $this->frontendUrl);
        $button = new SlackUrlButton(
            $requestsUrl,
            $this->translator->trans('slack.requests.goToRequests')
        );
        return new SlackActionBlock([ $button ]);
    }

    private function buildRequestInfoBlock() : SlackSendableMessage
    {
        $requestType = $this->getRequestType($this->request);
        $daysList = $this->createDaysList($this->request);
        $text = sprintf(
            $this->translator->trans('slack.requests.daysFormat'),
            $this->request->getId(),
            $requestType,
            $daysList
        );
        return new SlackContextBlock($text);
    }

    private function getRequestType(LeaveRequest $request) : string
    {
        /** @var LeaveRequestDay $firstDay */
        $firstDay = $request->getRequestDays()->get(0);
        switch ($firstDay->getType()) {
            case LeaveRequestDay::DAY_TYPE_FREE_PAID:
                return $this->translator->trans('slack.requests.type.freePaid');
            case LeaveRequestDay::DAY_TYPE_FREE_UNPAID:
                return $this->translator->trans('slack.requests.type.freeUnpaid');
            case LeaveRequestDay::DAY_TYPE_LEAVE_PAID:
                return $this->translator->trans('slack.requests.type.leavePaid');
            case LeaveRequestDay::DAY_TYPE_LEAVE_UNPAID:
                return $this->translator->trans('slack.requests.type.leaveUnpaid');
            case LeaveRequestDay::DAY_TYPE_LEAVE_REQUEST:
                return $this->translator->trans('slack.requests.type.leaveRequest');
            case LeaveRequestDay::DAY_TYPE_LEAVE_OCCASIONAL:
                return $this->translator->trans('slack.requests.type.leaveOccasional');
            case LeaveRequestDay::DAY_TYPE_LEAVE_CARE:
                return $this->translator->trans('slack.requests.type.leaveCare');
            case LeaveRequestDay::DAY_TYPE_SICK_LEAVE_PAID:
                return $this->translator->trans('slack.requests.type.sickLeavePaid');
            case LeaveRequestDay::DAY_TYPE_OVERTIME:
                return $this->translator->trans('slack.requests.type.leaveOvertime');
            case LeaveRequestDay::DAY_TYPE_SICK_LEAVE_UNPAID:
                return $this->translator->trans('slack.requests.type.sickLeaveUnpaid');
            case LeaveRequestDay::DAY_TYPE_ADDITIONAL_HOURS:
                return $this->translator->trans('slack.requests.type.additionalHours');
            case LeaveRequestDay::DAY_TYPE_UNAVAILABILITY:
                return $this->translator->trans('slack.requests.type.unavailabilityDay');
            default:
                return $this->translator->trans('slack.requests.type.unknown');
        }
    }

    private function createDaysList(LeaveRequest $request) : string
    {
        $buffer = '';
        /** @var LeaveRequestDay $requestDay */
        foreach ($request->getRequestDays()->toArray() as $requestDay) {
            $formatted = $requestDay->getDate()->format('j M Y');
            if (strlen($buffer) === 0) {
                $buffer = $formatted;
            } else {
                $buffer = "$buffer, $formatted";
            }
        }
        foreach (self::MONTHS_STRING as $key => $val) {
            $buffer = str_replace($val, $this->translator->trans($key), $buffer);
        }
        return $buffer;
    }
}
