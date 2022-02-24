<?php

namespace Divante\Bundle\AdventureBundle\Infrastructure\Config;

class EmailConfig
{
    private string $evidenceEmail;
    private string $techEmail;
    private string $fromEmail;
    private string $bokEmail;
    private string $accountantEmail;
    private string $admResponsibleEmail;
    private string $admPersonnelEmail;
    private string $helpdeskResponsibleEmail;
    private string $helpdeskDepartmentEmail;
    private string $receptionEmail;
    private string $employersRepresentative;
    private SystemConfig $systemConfig;

    /**
     * EmailConfig constructor.
     * @param array<string,string> $emailParameters
     * @param string $fromEmailAddress
     * @param SystemConfig $systemConfig
     */
    public function __construct(array $emailParameters, string $fromEmailAddress, SystemConfig $systemConfig)
    {
        $this->evidenceEmail = $emailParameters['evidence'];
        $this->techEmail = $emailParameters['tech'];
        $this->bokEmail = $emailParameters['bok'];
        $this->accountantEmail = $emailParameters['accountant'];
        $this->admResponsibleEmail = $emailParameters['adm_responsible'];
        $this->admPersonnelEmail = $emailParameters['adm_personnel'];
        $this->helpdeskResponsibleEmail = $emailParameters['helpdesk_responsible'];
        $this->helpdeskDepartmentEmail = $emailParameters['helpdesk_department'];
        $this->receptionEmail = $emailParameters['reception'];
        $this->fromEmail = $fromEmailAddress;
        $this->employersRepresentative = $emailParameters['employers_representative'];
        $this->systemConfig = $systemConfig;
    }

    public function getEvidenceEmail(): string
    {
        return $this->evidenceEmail;
    }

    public function getEvidenceEmailsByCompanyBranchAndContractType(
        string $branchOfCompany,
        string $contractType
    ): array {
        $const = "Divante\Bundle\AdventureBundle\Infrastructure\Config\SystemConfig::KEY_EVIDENCE_EMAIL_FOR_NOT_B2B_";
        if (strpos($contractType, 'B2B') !== false) {
            $const = "Divante\Bundle\AdventureBundle\Infrastructure\Config\SystemConfig::KEY_EVIDENCE_EMAIL_FOR_B2B_";
        }
        $evidenceKey = constant(
            $const.$branchOfCompany
        );
        $emails = $this->systemConfig->getValueOrDefault($evidenceKey, '');
        if ($emails !== '') {
            return explode(',', str_replace(' ', '', $emails));
        }
        return explode(',', $this->evidenceEmail);
    }

    public function getTechEmail(): string
    {
        return $this->techEmail;
    }

    public function getBokEmail(): string
    {
        return $this->bokEmail;
    }

    public function getBokEmailsByCompanyBranchAndContractType(string  $companyBranch, string $contractType): array
    {
        $const = "Divante\Bundle\AdventureBundle\Infrastructure\Config\SystemConfig::KEY_PAYROLL_EMAIL_FOR_NOT_B2B_";
        if (strpos($contractType, 'B2B') !== false) {
            $const = "Divante\Bundle\AdventureBundle\Infrastructure\Config\SystemConfig::KEY_PAYROLL_EMAIL_FOR_B2B_";
        }
        if (strpos($contractType, 'CoE') !== false) {
            $const = "Divante\Bundle\AdventureBundle\Infrastructure\Config\SystemConfig::KEY_PAYROLL_EMAIL_FOR_COE_";
        }
        $evidenceKey = constant(
            $const.$companyBranch
        );
        $emails = $this->systemConfig->getValueOrDefault($evidenceKey, '');
        if ($emails !== '') {
            return explode(',', str_replace(' ', '', $emails));
        }
        return explode(',', $this->bokEmail);
    }

    public function getAccountantEmail(): string
    {
        return $this->accountantEmail;
    }

    public function getAdmResponsibleEmail() : string
    {
        return $this->admResponsibleEmail;
    }

    public function getAdmPersonnelEmail() : string
    {
        return $this->admPersonnelEmail;
    }

    public function getHelpdeskResponsibleEmail() : string
    {
        return $this->helpdeskResponsibleEmail;
    }

    public function getHelpdeskDepartmentEmail() : string
    {
        return $this->helpdeskDepartmentEmail;
    }

    public function getReceptionEmail() : string
    {
        return $this->receptionEmail;
    }

    public function getFromEmail(): string
    {
        return $this->fromEmail;
    }

    public function getEmployersRepresentative(): string
    {
        return $this->employersRepresentative;
    }
}
