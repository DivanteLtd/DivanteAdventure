<?php

namespace Divante\Bundle\AdventureBundle\Message\Checklist;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class CreateChecklistTemplateQuestion
{
    use ObjectTrait;

    private int $templateId;
    private string $namePl;
    private string $nameEn;
    private string $descriptionPl;
    private string $descriptionEn;
    /** @var array<int,array<string,string>> */
    private array $possibleStatuses;
    private ?int $responsibleId;

    /**
     * CreateChecklistTemplateQuestion constructor.
     * @param int $templateId
     * @param string $namePl
     * @param string $nameEn
     * @param string $descriptionPl
     * @param string $descriptionEn
     * @param array<int,array<string,string>> $possibleStatuses
     * @param int|null $responsibleId
     */
    public function __construct(
        int $templateId,
        string $namePl,
        string $nameEn,
        string $descriptionPl,
        string $descriptionEn,
        array $possibleStatuses,
        ?int $responsibleId
    ) {
        $this->templateId = $templateId;
        $this->namePl = $namePl;
        $this->nameEn = $nameEn;
        $this->descriptionPl = $descriptionPl;
        $this->descriptionEn = $descriptionEn;
        $this->possibleStatuses = $possibleStatuses;
        $this->responsibleId = $responsibleId;
    }

    public function getTemplateId(): int
    {
        return $this->templateId;
    }

    public function getNamePl(): string
    {
        return $this->namePl;
    }

    public function getNameEn(): string
    {
        return $this->nameEn;
    }

    public function getDescriptionPl(): string
    {
        return $this->descriptionPl;
    }

    public function getDescriptionEn(): string
    {
        return $this->descriptionEn;
    }

    /** @return array<int,array<string,string>> */
    public function getPossibleStatuses(): array
    {
        return $this->possibleStatuses;
    }

    public function getResponsibleId(): ?int
    {
        return $this->responsibleId;
    }
}
