<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 22.03.19
 * Time: 11:43
 */

namespace Divante\Bundle\AdventureBundle\Services\RequestMailer;

use Divante\Bundle\AdventureBundle\Entity\LeaveRequest;
use Divante\Bundle\AdventureBundle\Entity\LeaveRequestDay;
use Divante\Bundle\AdventureBundle\Infrastructure\Config\EmailConfig;
use Divante\Bundle\AdventureBundle\Representation\Email\EmailConfiguration;
use Divante\Bundle\AdventureBundle\Infrastructure\Config\FrontendUrlSupplier;
use Divante\Bundle\AdventureBundle\Services\RequestMailer\Templates\AbstractTemplate;
use Divante\Bundle\AdventureBundle\Services\RequestMailer\Templates\NullTemplate;
use Divante\Bundle\AdventureBundle\Services\RequestMailer\Templates\RequestAcceptedTemplate;
use Divante\Bundle\AdventureBundle\Services\RequestMailer\Templates\RequestPendingResignationTemplate;
use Divante\Bundle\AdventureBundle\Services\RequestMailer\Templates\RequestrejectedTemplate;
use Divante\Bundle\AdventureBundle\Services\RequestMailer\Templates\RequestResignedTemplate;
use Divante\Bundle\AdventureBundle\Services\RequestMailer\Templates\ResignationAcceptedTemplate;
use Divante\Bundle\AdventureBundle\Services\RequestMailer\Templates\RequestOvertimeResignationTemplate;
use Divante\Bundle\AdventureBundle\Services\RequestMailer\Templates\ResignationRejectedTemplate;
use Divante\Bundle\AdventureBundle\Services\RequestMailer\Templates\RequestConfirmedTemplate;
use Symfony\Component\Translation\TranslatorInterface;

class TemplateFactory
{
    private EmailConfiguration $emailSender;
    private array $admEmail;
    private string $techEmail;
    private string $accountantEmail;
    private string $employersRepresentative;
    private string $frontendUrl;
    private EmailConfig $emailConfig;
    protected TranslatorInterface $translator;

    public function __construct(
        FrontendUrlSupplier $frontendUrlSupplier,
        EmailConfiguration $emailSender,
        EmailConfig $emailConfig,
        TranslatorInterface $translator
    ) {
        $this->emailSender = $emailSender;
        $this->techEmail = $emailConfig->getTechEmail();
        $this->accountantEmail = $emailConfig->getAccountantEmail();
        $this->frontendUrl = $frontendUrlSupplier->getFrontendUrl();
        $this->employersRepresentative = $emailConfig->getEmployersRepresentative();
        $this->translator = $translator;
        $this->emailConfig = $emailConfig;
    }

    public function getTemplate(
        int $oldStatus,
        int $newStatus,
        LeaveRequest $request,
        string $branchOfCompany,
        string $contractType
    ): AbstractTemplate {
        $this->admEmail = $this->emailConfig->getBokEmailsByCompanyBranchAndContractType(
            $branchOfCompany,
            $contractType
        );
        if (($oldStatus === LeaveRequest::REQUEST_STATUS_PENDING || $oldStatus === LeaveRequest::REQUEST_STATUS_PLANNED)
            && $newStatus === LeaveRequest::REQUEST_STATUS_ACCEPTED
        ) {
            return new RequestAcceptedTemplate(
                $this->admEmail,
                $this->techEmail,
                $this->accountantEmail,
                $this->frontendUrl,
                $request,
                $this->translator,
                $this->employersRepresentative
            );
        } elseif (($oldStatus === LeaveRequest::REQUEST_STATUS_PENDING
            || $oldStatus === LeaveRequest::REQUEST_STATUS_PLANNED)
            && $newStatus === LeaveRequest::REQUEST_STATUS_RESIGNED
        ) {
            return new RequestResignedTemplate(
                $this->admEmail,
                $this->techEmail,
                $this->accountantEmail,
                $this->frontendUrl,
                $request,
                $this->translator,
                ''
            );
        } elseif ($oldStatus === LeaveRequest::REQUEST_STATUS_PLANNED
            && $newStatus === LeaveRequest::REQUEST_STATUS_PENDING
        ) {
            return new RequestConfirmedTemplate(
                $this->admEmail,
                $this->techEmail,
                $this->accountantEmail,
                $this->frontendUrl,
                $request,
                $this->translator,
                ''
            );
        } elseif (($oldStatus === LeaveRequest::REQUEST_STATUS_PENDING
                || $oldStatus === LeaveRequest::REQUEST_STATUS_PLANNED)
            && $newStatus === LeaveRequest::REQUEST_STATUS_REJECTED
        ) {
            return new RequestrejectedTemplate(
                $this->admEmail,
                $this->techEmail,
                $this->accountantEmail,
                $this->frontendUrl,
                $request,
                $this->translator,
                ''
            );
        } elseif ($oldStatus === LeaveRequest::REQUEST_STATUS_ACCEPTED
            && $newStatus === LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION
            && $request->getRequestDays()[0]->getType() === LeaveRequestDay::DAY_TYPE_OVERTIME
        ) {
            return new RequestOvertimeResignationTemplate(
                $this->admEmail,
                $this->techEmail,
                $this->accountantEmail,
                $this->frontendUrl,
                $request,
                $this->translator,
                ''
            );
        } elseif ($oldStatus === LeaveRequest::REQUEST_STATUS_ACCEPTED
            && $newStatus === LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION
        ) {
            return new RequestPendingResignationTemplate(
                $this->admEmail,
                $this->techEmail,
                $this->accountantEmail,
                $this->frontendUrl,
                $request,
                $this->translator,
                ''
            );
        } elseif ($oldStatus === LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION
            && $newStatus === LeaveRequest::REQUEST_STATUS_RESIGNED
        ) {
            return new ResignationAcceptedTemplate(
                $this->admEmail,
                $this->techEmail,
                $this->accountantEmail,
                $this->frontendUrl,
                $request,
                $this->translator,
                ''
            );
        } elseif ($oldStatus === LeaveRequest::REQUEST_STATUS_PENDING_RESIGNATION
            && $newStatus === LeaveRequest::REQUEST_STATUS_ACCEPTED
        ) {
            return new ResignationRejectedTemplate(
                $this->admEmail,
                $this->techEmail,
                $this->accountantEmail,
                $this->frontendUrl,
                $request,
                $this->translator,
                ''
            );
        } else {
            return new NullTemplate(
                $this->admEmail,
                $this->techEmail,
                $this->accountantEmail,
                $this->frontendUrl,
                $request,
                $this->translator,
                ''
            );
        }
    }
}
