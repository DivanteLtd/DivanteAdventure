<?php

namespace Divante\Bundle\AdventureBundle\Entity\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Divante\Bundle\AdventureBundle\Entity\Data\Timestampable;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Doctrine\ORM\Mapping as ORM;

abstract class AbstractChecklistQuestion implements ChecklistQuestionInterface
{
    use Id, Timestampable;

    /** @ORM\Column(name="name_pl", type="string") */
    protected string $namePl;
    /** @ORM\Column(name="name_en", type="string") */
    protected string $nameEn;
    /** @ORM\Column(name="description_pl", type="string") */
    protected string $descriptionPl;
    /** @ORM\Column(name="description_en", type="string") */
    protected string $descriptionEn;
    /**
     * @ORM\Column(name="possible_statuses", type="json")
     * @var array<int,array<string,string>>
     */
    protected array $possibleStatuses;
    /**
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Employee")
     * @ORM\JoinColumn(name="responsible_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected ?Employee $responsible;

    public function getNamePl(): string
    {
        return $this->namePl;
    }

    public function setNamePl(string $namePl) : void
    {
        $this->namePl = $namePl;
    }

    public function getNameEn(): string
    {
        return $this->nameEn;
    }

    public function setNameEn(string $nameEn) : void
    {
        $this->nameEn = $nameEn;
    }

    public function getDescriptionPl(): string
    {
        return $this->descriptionPl;
    }

    public function setDescriptionPl(string $descriptionPl) : void
    {
        $this->descriptionPl = $descriptionPl;
    }

    public function getDescriptionEn(): string
    {
        return $this->descriptionEn;
    }

    public function setDescriptionEn(string $descriptionEn) : void
    {
        $this->descriptionEn = $descriptionEn;
    }

    /** @inheritDoc */
    public function getPossibleStatuses(): array
    {
        return $this->possibleStatuses;
    }

    /** @param array<int,array<string,string>> $possibleStatuses */
    public function setPossibleStatuses(array $possibleStatuses) : void
    {
        $this->possibleStatuses = $possibleStatuses;
    }

    public function getResponsible(): ?Employee
    {
        return $this->responsible;
    }

    public function setResponsible(?Employee $responsible) : void
    {
        $this->responsible = $responsible;
    }
}
