<?php

namespace Divante\Bundle\AdventureBundle\Entity\Hardware;

use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Divante\Bundle\AdventureBundle\Entity\Data\Timestampable;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Doctrine\ORM\Mapping as ORM;

/**
 * HardwareAgreement
 *
 * @ORM\Table(name="hardware_agreement")
 * @ORM\Entity(repositoryClass="Divante\Bundle\AdventureBundle\Entity\Repository\Hardware\HardwareAgreementRepository")
 */
class HardwareAgreement
{
    use Id, Timestampable;

    /** @ORM\Column(name="signed_by_helpdesk", type="datetime", nullable=true) */
    private ?\DateTime $signedByHelpdesk = null;

    /** @ORM\Column(name="signed_by_user", type="datetime", nullable=true) */
    private ?\DateTime $signedByUser = null;
    /** @ORM\Column(name="date_generated", type="datetime", nullable=true) */
    private ?\DateTime $generated = null;
    /** @ORM\Column(name="pesel", type="text", length=100, nullable=true, columnDefinition="blob") */
    private ?string $pesel = null;
    /** @ORM\Column(name="nip", type="text", length=100, nullable=true, columnDefinition="blob") */
    private ?string $nip = null;
    /** @ORM\Column(name="company", type="text", length=500, nullable=true, columnDefinition="blob") */
    private ?string $company = null;
    /** @ORM\Column(name="headquarters", type="text", length=500, nullable=true, columnDefinition="blob") */
    private ?string $headquarters = null;
    /** @ORM\Column(name="password_hashed", type="text", length=255, nullable=true) */
    private ?string $passwordHashed = null;
    /**
     * @var string[]
     * @ORM\Column(name="use_languages", type="simple_array", length=255)
     */
    private array $useLanguages = ['pl'];
    /**
     * @ORM\OneToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Hardware\HardwareAssignment")
     * @ORM\JoinColumn(name="assignment_id", nullable=true, referencedColumnName="id", onDelete="CASCADE")
     */
    private ?HardwareAssignment $assignment = null;
    /**
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Employee")
     * @ORM\JoinColumn(name="helpdesk_signer_id", nullable=true, referencedColumnName="id", onDelete="CASCADE")
     */
    private ?Employee $helpdeskSigner = null;

    /** @ORM\Column(name="employeeName", type="text", length=255, nullable=true) */
    private ?string $name = null;
    /** @ORM\Column(name="employeeLastName", type="text", length=255, nullable=true) */
    private ?string $lastName = null;

    public function setName(string $name) : self
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): string
    {
        if (!is_null($this->name)) {
            return $this->name;
        }
        return !is_null($this->assignment->getEmployee()) ? $this->assignment->getEmployee()->getName()
            : $this->assignment->getPotentialEmployee()->getName();
    }

    public function setLastName(string $lastName) : self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getLastName(): string
    {
        if (!is_null($this->lastName)) {
            return $this->lastName;
        }
        return !is_null($this->assignment->getEmployee()) ? $this->assignment->getEmployee()->getLastName()
            : $this->assignment->getPotentialEmployee()->getLastName();
    }

    public function getSignedByHelpdesk() : ?\DateTime
    {
        return $this->signedByHelpdesk;
    }

    public function setSignedByHelpdesk(?\DateTime $signedByHelpdesk) : self
    {
        $this->signedByHelpdesk = $signedByHelpdesk;
        return $this;
    }

    public function getSignedByUser() : ?\DateTime
    {
        return $this->signedByUser;
    }

    public function setSignedByUser(?\DateTime $signedByUser) : self
    {
        $this->signedByUser = $signedByUser;
        return $this;
    }

    public function getGenerationDate(): ?\DateTime
    {
        return $this->generated;
    }

    public function setGenerationDate(?\DateTime $generated): self
    {
        $this->generated = $generated;
        return $this;
    }

    public function setPesel(?string $pesel): self
    {
        $this->pesel = $pesel;
        return $this;
    }

    public function getPesel(): ?string
    {
        return $this->pesel;
    }

    public function setNip(?string $nip): self
    {
        $this->nip = $nip;
        return $this;
    }

    public function getNip(): ?string
    {
        return $this->nip;
    }

    public function setCompany(?string $company): self
    {
        $this->company = $company;
        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setHeadquarters(?string $headquarters): self
    {
        $this->headquarters = $headquarters;
        return $this;
    }

    public function getHeadquarters(): ?string
    {
        return $this->headquarters;
    }

    public function setPasswordHashed(?string $passwordHashed): self
    {
        $this->passwordHashed = $passwordHashed;
        return $this;
    }

    public function getPasswordHashed(): ?string
    {
        return $this->passwordHashed;
    }

    public function getAssignment(): ?HardwareAssignment
    {
        return $this->assignment;
    }

    public function setAssignment(?HardwareAssignment $assignment): self
    {
        $this->assignment = $assignment;
        return $this;
    }

    public function getHelpdeskSigner(): ?Employee
    {
        return $this->helpdeskSigner;
    }

    public function setHelpdeskSigner(?Employee $helpdeskSigner): self
    {
        $this->helpdeskSigner = $helpdeskSigner;
        return $this;
    }

    /** @return string[] */
    public function getUseLanguages(): array
    {
        return $this->useLanguages;
    }

    /**
     * @param string[] $useLanguages
     * @return $this
     */
    public function setUseLanguages(array $useLanguages): self
    {
        $this->useLanguages = $useLanguages;
        return $this;
    }
}
