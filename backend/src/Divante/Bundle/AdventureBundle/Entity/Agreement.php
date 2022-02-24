<?php

namespace Divante\Bundle\AdventureBundle\Entity;

use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Divante\Bundle\AdventureBundle\Entity\Data\Timestampable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Agreement
 *
 * @ORM\Table(name="agreement")
 * @ORM\Entity
 */
class Agreement
{
    use Timestampable, Id;
    const TYPE_GDPR = 0;
    const TYPE_MARKETING = 1;
    const TYPE_FIRE_SAFETY = 2;
    const TYPE_ISO = 3;

    /** @ORM\Column(name="name_pl", type="string", length=50, nullable=false) */
    private string $namePl;
    /** @ORM\Column(name="name_en", type="string", length=50, nullable=false) */
    private string $nameEn;
    /** @ORM\Column(name="descriptionPl", type="text", length=65535, nullable=false) */
    private string $descriptionPl;
    /** @ORM\Column(name="descriptionEn", type="text", length=65535, nullable=false) */
    private string $descriptionEn;
    /** @ORM\Column(name="priority", type="integer", nullable=false) */
    private int $priority = 0;
    /** @ORM\Column(name="required", type="boolean", nullable=false) */
    private bool $required = false;
    /** @ORM\Column(name="type", type="integer", nullable=false) */
    private int $type;
    /** @var int[] @ORM\Column(name="contract_ids", type="simple_array", nullable=false) */
    private array $contractIds = [];

    /**
     * @var Collection<int,AgreementAttachment>
     * @ORM\ManyToMany(targetEntity="AgreementAttachment")
     * @ORM\JoinTable(name="agreement_attachment",
     *      joinColumns={@ORM\JoinColumn(name="agreement_id", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="attachment_id", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    private Collection $attachments;

    public function __construct()
    {
        $this->attachments = new ArrayCollection();
    }

    public function getNamePl() : string
    {
        return $this->namePl;
    }

    public function setNamePl(string $namePl) : self
    {
        $this->namePl = $namePl;
        return $this;
    }

    public function getNameEn() : string
    {
        return $this->nameEn;
    }

    public function setNameEn(string $nameEn) : self
    {
        $this->nameEn = $nameEn;
        return $this;
    }

    public function getDescriptionPl() : string
    {
        return $this->descriptionPl;
    }

    public function setDescriptionPl(string $descriptionPl) : self
    {
        $this->descriptionPl = $descriptionPl;
        return $this;
    }

    public function getDescriptionEn() : string
    {
        return $this->descriptionEn;
    }

    public function setDescriptionEn(string $descriptionEn) : self
    {
        $this->descriptionEn = $descriptionEn;
        return $this;
    }

    public function getPriority() : int
    {
        return $this->priority;
    }

    public function setPriority(int $priority) : self
    {
        $this->priority = $priority;
        return $this;
    }

    public function isRequired() : bool
    {
        return $this->required;
    }

    public function setRequired(bool $required) : self
    {
        $this->required = $required;
        return $this;
    }

    /** @return int[] */
    public function getContractIds(): array
    {
        return $this->contractIds;
    }

    /**
     * @param int[] $contractIds
     * @return Agreement
     */
    public function setContractIds(array $contractIds): self
    {
        $this->contractIds = $contractIds;
        return $this;
    }

    /** @return Collection<int,AgreementAttachment> */
    public function getAttachments() : Collection
    {
        return $this->attachments;
    }

    public function getType() : int
    {
        return $this->type;
    }

    public function setType(int $type) : self
    {
        $this->type = $type;
        return $this;
    }
}
