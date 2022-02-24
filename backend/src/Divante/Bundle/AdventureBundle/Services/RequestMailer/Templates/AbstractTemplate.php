<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 22.03.19
 * Time: 10:50
 */

namespace Divante\Bundle\AdventureBundle\Services\RequestMailer\Templates;

use Divante\Bundle\AdventureBundle\Entity\LeaveRequest;
use Divante\Bundle\AdventureBundle\Entity\LeaveRequestDay;
use Symfony\Component\Translation\TranslatorInterface;

abstract class AbstractTemplate
{
    public const DATE_FORMAT = 'Y-m-d H:i:s';

    public const SPECIAL_TYPE_SIGNS = [
        '☘' => [ LeaveRequestDay::DAY_TYPE_LEAVE_OCCASIONAL ],
        "\u{26C7}" => [ LeaveRequestDay::DAY_TYPE_SICK_LEAVE_PAID, LeaveRequestDay::DAY_TYPE_SICK_LEAVE_UNPAID ],
        '♚' => [ LeaveRequestDay::DAY_TYPE_LEAVE_CARE ]
    ];

    private array $admEmail;
    private string $techEmail;
    private string $employersRepresentative;
    private string $accountantEmail;
    private string $frontendUrl;
    private LeaveRequest $request;
    protected TranslatorInterface $translator;

    public function __construct(
        array $admMail,
        string $techEmail,
        string $accountantEmail,
        string $frontendUrl,
        LeaveRequest $request,
        TranslatorInterface $translator,
        string $employersRepresentative
    ) {
        $this->request = $request;
        $this->admEmail = $admMail;
        $this->techEmail = $techEmail;
        $this->accountantEmail = $accountantEmail;
        $this->frontendUrl = $frontendUrl;
        $this->translator = $translator;
        $this->employersRepresentative = $employersRepresentative;
    }

    protected function getAdminEmails(): array
    {
        return $this->admEmail;
    }

    protected function getAdventureDivEmail() : string
    {
        return $this->techEmail;
    }

    protected function getAccountantEmail() : string
    {
        return $this->accountantEmail;
    }

    protected function getRequest() : LeaveRequest
    {
        return $this->request;
    }

    /** @return array<string,mixed> */
    public function getData() : array
    {
        return [
            'request' => $this->request,
            'dateNow' => date(self::DATE_FORMAT),
            'frontend_app_url' => $this->frontendUrl,
            'employersRepresentative' => $this->employersRepresentative,
        ];
    }

    public function getSpecialTypeSign() : string
    {
        $days = $this->request->getRequestDays();
        /** @var LeaveRequestDay $day */
        foreach ($days as $day) {
            $type = $day->getType();
            foreach (self::SPECIAL_TYPE_SIGNS as $character => $types) {
                if (in_array($type, $types)) {
                    return '['.$character.']';
                }
            }
        }
        return '';
    }

    abstract public function getSubject() : string;

    abstract public function getEmployeeSubject() : string;
    /** @return string[] */
    abstract public function getEmailsWithTemplates() : array;
    /** @return string[] */
    abstract public function getEmployeeEmailWithTemplate() : array;
}
