<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 * Date: 13.02.19
 * Time: 11:55
 */

namespace Divante\Bundle\AdventureBundle\Message;

use Divante\Bundle\AdventureBundle\Entity\Tribe;
use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class UpdateTribe
{
    use ObjectTrait;

    private Tribe $entry;
    private string $name;
    private string $description;
    private string $photoUrl;
    private string $url;
    private bool $isVirtual;
    private array $responsible;
    private string $hrEmail;
    private ?string $freeDayProjectId;
    private ?string $freeDayCategoryId;
    private ?string $sickLeaveProjectId;
    private ?string $sickLeaveCategoryId;
    private ?int $techLeaderId;

    public function __construct(
        Tribe $entry,
        string $name,
        string $description,
        string $photoUrl,
        string $url,
        bool $isVirtual,
        array $responsible,
        string $hrEmail,
        ?string $freeDayProjectId,
        ?string $freeDayCategoryId,
        ?string $sickLeaveProjectId,
        ?string $sickLeaveCategoryId,
        ?int $techLeaderId
    ) {
        $this->entry = $entry;
        $this->name = $name;
        $this->description = $description;
        $this->photoUrl = $photoUrl;
        $this->url = $url;
        $this->isVirtual = $isVirtual;
        $this->responsible = $responsible;
        $this->hrEmail = $hrEmail;
        $this->freeDayProjectId = $freeDayProjectId;
        $this->freeDayCategoryId = $freeDayCategoryId;
        $this->sickLeaveProjectId = $sickLeaveProjectId;
        $this->sickLeaveCategoryId = $sickLeaveCategoryId;
        $this->techLeaderId = $techLeaderId;
    }

    public function getEntry(): Tribe
    {
        return $this->entry;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPhotoUrl(): string
    {
        return $this->photoUrl;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function isVirtual(): bool
    {
        return $this->isVirtual;
    }

    public function getResponsible(): array
    {
        return $this->responsible;
    }

    public function getHrEmail(): string
    {
        return $this->hrEmail;
    }

    public function getFreeDayProjectId(): ?string
    {
        return $this->freeDayProjectId;
    }

    public function getFreeDayCategoryId(): ?string
    {
        return $this->freeDayCategoryId;
    }

    public function getSickLeaveProjectId(): ?string
    {
        return $this->sickLeaveProjectId;
    }

    public function getSickLeaveCategoryId(): ?string
    {
        return $this->sickLeaveCategoryId;
    }

    public function getTechLeaderId(): ?int
    {
        return $this->techLeaderId;
    }
}
