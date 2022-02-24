<?php

namespace Divante\Bundle\AdventureBundle\Entity;

use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Divante\Bundle\AdventureBundle\Entity\Data\NamedEntity;
use Divante\Bundle\AdventureBundle\Entity\Data\SlackEntity;
use Divante\Bundle\AdventureBundle\Entity\Data\Timestampable;
use Divante\Bundle\AdventureBundle\Services\Slack\Api\SlackReceiver;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="tribe")
 * @ORM\Entity
 */
class Tribe implements NamedEntity, SlackReceiver
{
    use Timestampable, Id, SlackEntity;

    /** @ORM\Column(name="description", type="text", length=65535, nullable=true) */
    private ?string $description = null;
    /** @ORM\Column(name="photo", type="string", length=255, nullable=true) */
    private ?string $photo = null;
    /** @ORM\Column(name="url", type="string", length=255, nullable=true) */
    private ?string $url = null;
    /** @ORM\Column(name="is_virtual", type="boolean", nullable=false) */
    private bool $isVirtual = false;
    /** @ORM\Column(name="visibility", type="integer", nullable=true) */
    private ?int $visibility = null;
    /** @ORM\Column(name="name", type="string", length=50, nullable=false) */
    private string $name = '';
    /** @ORM\Column(name="hr_email", type="string", length=254, nullable=false) */
    private string $hrEmail = '';
    /** @ORM\Column(name="sick_leave_project_id", type="string", length=50, nullable=true) */
    private ?string $sickLeaveProjectId = null;
    /** @ORM\Column(name="sick_leave_category_id", type="string", length=50, nullable=true) */
    private ?string $sickLeaveCategoryId = null;
    /** @ORM\Column(name="free_day_project_id", type="string", length=50, nullable=true) */
    private ?string $freeDayProjectId = null;
    /** @ORM\Column(name="free_day_category_id", type="string", length=50, nullable=true) */
    private ?string $freeDayCategoryId = null;
    /**
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Employee")
     * @ORM\JoinColumn(name="tech_leader", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private ?Employee $techLeader = null;
    /**
     * @var Collection<int,Employee>
     * @ORM\ManyToMany(targetEntity="Divante\Bundle\AdventureBundle\Entity\Employee", inversedBy="tribeResponsible")
     * @ORM\JoinTable(name="tribe_responsible",
     *     joinColumns={@ORM\JoinColumn(name="tribe_id", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="employee_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    private Collection $responsible;

    /**
     * @var Collection<int,Employee>
     * @ORM\OneToMany(targetEntity="Divante\Bundle\AdventureBundle\Entity\Employee", mappedBy="tribe")
     */
    private Collection $employees;

    /**
     * @var Collection<int,Position>
     * @ORM\ManyToMany(targetEntity="Divante\Bundle\AdventureBundle\Entity\Position", mappedBy="tribes")
     */
    private Collection $positions;

    public function __construct()
    {
        $this->employees = new ArrayCollection();
        $this->positions = new ArrayCollection();
        $this->responsible = new ArrayCollection();
    }

    public function getDescription() : ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description) : self
    {
        $this->description = $description;
        return $this;
    }

    public function getPhoto() : ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo) : self
    {
        $this->photo = $photo;
        return $this;
    }

    public function getUrl() : ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url) : self
    {
        $this->url = $url;
        return $this;
    }

    /** @return Collection<int,Employee> */
    public function getEmployees() : Collection
    {
        return $this->employees;
    }

    /** @return Collection<int,Position> */
    public function getPositions() : Collection
    {
        return $this->positions;
    }

    /** @return Collection<int,Employee> */
    public function getResponsible() : Collection
    {
        return $this->responsible;
    }

    public function isVirtual() : bool
    {
        return $this->isVirtual;
    }

    public function setVirtual(bool $virtual = true) : self
    {
        $this->isVirtual = $virtual;
        return $this;
    }

    public function getVisibility() : ?int
    {
        return $this->visibility;
    }

    public function setVisibility(?int $visibility) : self
    {
        $this->visibility = $visibility;
        return $this;
    }

    public function getSlackType(): string
    {
        return 'AdventureBundle:Tribe';
    }

    public function getSlackLanguage(): string
    {
        return 'en';
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function setName(string $name) : self
    {
        $this->name = $name;
        return $this;
    }

    public function getHrEmail(): string
    {
        return $this->hrEmail;
    }

    public function setHrEmail(string $hrEmail): self
    {
        $this->hrEmail = $hrEmail;
        return $this;
    }

    public function getSickLeaveCategoryId(): ?string
    {
        return $this->sickLeaveCategoryId;
    }

    public function setSickLeaveCategoryId(?string $sickLeaveCategoryId): self
    {
        $this->sickLeaveCategoryId = $sickLeaveCategoryId;
        return $this;
    }

    public function getSickLeaveProjectId(): ?string
    {
        return $this->sickLeaveProjectId;
    }

    public function setSickLeaveProjectId(?string $sickLeaveProjectId): self
    {
        $this->sickLeaveProjectId = $sickLeaveProjectId;
        return $this;
    }

    public function getFreeDayCategoryId(): ?string
    {
        return $this->freeDayCategoryId;
    }

    public function setFreeDayCategoryId(?string $freeDayCategoryId): self
    {
        $this->freeDayCategoryId = $freeDayCategoryId;
        return $this;
    }

    public function getFreeDayProjectId(): ?string
    {
        return $this->freeDayProjectId;
    }

    public function setFreeDayProjectId(?string $freeDayProjectId): self
    {
        $this->freeDayProjectId = $freeDayProjectId;
        return $this;
    }

    public function getTechLeader() : ?Employee
    {
        return $this->techLeader;
    }

    public function setTechLeader(?Employee $techLeader) : self
    {
        $this->techLeader = $techLeader;
        return $this;
    }
}
