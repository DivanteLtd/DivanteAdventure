<?php

namespace Divante\Bundle\AdventureBundle\Message\Checklist;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class EditChecklistTemplateQuestion
{
    use ObjectTrait;

    private int $templateId;
    private int $questionId;
    private ?string $namePl;
    private ?string $nameEn;
    private ?string $descriptionPl;
    private ?string $descriptionEn;
    /** @var null|array<int,array<string,string>> */
    private ?array $possibleStatuses;
    private ?int $responsibleId;

    /**
     * EditChecklistTemplateQuestion constructor.
     * @param int $templateId
     * @param int $questionId
     * @param string|null $namePl
     * @param string|null $nameEn
     * @param string|null $descriptionPl
     * @param string|null $descriptionEn
     * @param null|array<int,array<string,string>> $possibleStatuses
     * @param int|null $responsibleId
     */
    public function __construct(
        int $templateId,
        int $questionId,
        ?string $namePl,
        ?string $nameEn,
        ?string $descriptionPl,
        ?string $descriptionEn,
        ?array $possibleStatuses,
        ?int $responsibleId
    ) {
        $this->templateId = $templateId;
        $this->questionId = $questionId;
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

    public function getQuestionId(): int
    {
        return $this->questionId;
    }

    public function getNamePl(): ?string
    {
        return $this->namePl;
    }

    public function getNameEn(): ?string
    {
        return $this->nameEn;
    }

    public function getDescriptionPl(): ?string
    {
        return $this->descriptionPl;
    }

    public function getDescriptionEn(): ?string
    {
        return $this->descriptionEn;
    }

    /** @return null|array<int,array<string,string>> */
    public function getPossibleStatuses(): ?array
    {
        return $this->possibleStatuses;
    }

    public function getResponsibleId(): ?int
    {
        return $this->responsibleId;
    }
}
