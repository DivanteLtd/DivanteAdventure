<?php
/**
 * Created by PhpStorm.
 * User: pawel
 * Date: 16.01.19
 * Time: 12:20
 */

namespace Divante\Bundle\AdventureBundle\Query\Agreement;

class AgreementAttachmentView
{
    private int $id;
    private string $descriptionPl;
    private string $descriptionEn;
    private string $agreementNamePl;
    private string $agreementNameEn;
    private int $required;
    private int $priority;
    private int $accepted;
    private int $type;
    /** @var string[] */
    private $attachmentsId;
    /** @var string[] */
    private $name;
    /** @var string[] */
    private $contracts;

    /**
     * AgreementAttachmentView constructor.
     * @param int $id
     * @param string $descriptionPl
     * @param string $descriptionEn
     * @param string $agreementNamePl
     * @param string $agreementNameEn
     * @param int $required
     * @param int $priority
     * @param string[] $attachmentsId
     * @param string[] $name
     * @param string[] $contracts
     * @param int $accepted
     * @param int $type
     */
    public function __construct(
        int $id,
        string $descriptionPl,
        string $descriptionEn,
        string $agreementNamePl,
        string $agreementNameEn,
        int $required,
        int $priority,
        array $attachmentsId,
        array $name,
        array $contracts,
        int $accepted,
        int $type
    ) {
        $this->id = $id;
        $this->descriptionPl = $descriptionPl;
        $this->descriptionEn = $descriptionEn;
        $this->agreementNamePl = $agreementNamePl;
        $this->agreementNameEn = $agreementNameEn;
        $this->required = $required;
        $this->priority = $priority;
        $this->attachmentsId = $attachmentsId;
        $this->name = $name;
        $this->contracts = $contracts;
        $this->accepted = $accepted;
        $this->type = $type;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDescriptionPl(): string
    {
        return $this->descriptionPl;
    }

    public function getDescriptionEn(): string
    {
        return $this->descriptionEn;
    }

    public function getAgreementNamePl(): string
    {
        return $this->agreementNamePl;
    }

    public function getAgreementNameEn(): string
    {
        return $this->agreementNameEn;
    }

    public function getRequired(): int
    {
        return $this->required;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    /** @return string[] */
    public function getAttachmentsId(): array
    {
        return $this->attachmentsId;
    }

    /** @return string[] */
    public function getName(): array
    {
        return $this->name;
    }

    /** @return string[] */
    public function getContracts(): array
    {
        return $this->contracts;
    }

    public function getAccepted(): int
    {
        return $this->accepted;
    }

    public function getType(): int
    {
        return $this->type;
    }
}
