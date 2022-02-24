<?php

namespace Divante\Bundle\AdventureBundle\Message\Agreement;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

abstract class AbstractAgreementMessage
{
    use ObjectTrait;

    private string $namePl;
    private string $nameEn;
    private string $descriptionPl;
    private string $descriptionEn;
    private bool $required;
    private int $priority;
    private int $type;
    /** @var int[] */
    private array $contracts;
    /** @var int[] */
    private array $attachments;

    /**
     * AbstractAgreementMessage constructor.
     * @param string $namePl
     * @param string $nameEn
     * @param string $descriptionPl
     * @param string $descriptionEn
     * @param bool $required
     * @param int $priority
     * @param int[] $contracts
     * @param int[] $attachments
     * @param int $type
     */
    public function __construct(
        string $namePl,
        string $nameEn,
        string $descriptionPl,
        string $descriptionEn,
        bool $required,
        int $priority,
        array $contracts,
        array $attachments,
        int $type
    ) {
        $this->namePl = $namePl;
        $this->nameEn = $nameEn;
        $this->descriptionPl = $descriptionPl;
        $this->descriptionEn = $descriptionEn;
        $this->required = $required;
        $this->priority = $priority;
        $this->contracts = $contracts;
        $this->attachments = $attachments;
        $this->type = $type;
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

    public function isRequired() : bool
    {
        return $this->required;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    /** @return int[] */
    public function getContracts(): array
    {
        return $this->contracts;
    }

    /** @return int[] */
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    public function getType(): int
    {
        return $this->type;
    }
}
