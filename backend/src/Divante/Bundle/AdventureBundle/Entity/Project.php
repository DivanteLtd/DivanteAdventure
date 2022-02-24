<?php

namespace Divante\Bundle\AdventureBundle\Entity;

use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Divante\Bundle\AdventureBundle\Entity\Data\SlackEntity;
use Divante\Bundle\AdventureBundle\Entity\Data\Timestampable;
use Divante\Bundle\AdventureBundle\Services\Slack\Api\SlackReceiver;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Project
 *
 * @ORM\Table(name="project")
 * @ORM\Entity
 */
class Project implements SlackReceiver
{
    use Timestampable, Id, SlackEntity;

    public const PROJECT_TYPE_UNDEFINED = 0;
    public const PROJECT_TYPE_IMPLEMENTATION = 1;
    public const PROJECT_TYPE_MAINTENANCE = 2;

    public const PROJECT_HOURS_SUM_MONTHLY = 0;
    public const PROJECT_HOURS_SUM_ALL = 1;

    /** @ORM\Column(name="photo", type="string", length=255, nullable=true) */
    private ?string $photo = null;
    /** @ORM\Column(name="url", type="string", length=255, nullable=true) */
    private ?string $url = null;
    /** @ORM\Column(name="description", type="text", length=65535, nullable=true) */
    private ?string $description = null;
    /** @ORM\Column(name="started_at", type="string", nullable=true) */
    private ?string $startedAt = null;
    /** @ORM\Column(name="ended_at", type="string", nullable=true) */
    private ?string $endedAt = null;
    /** @ORM\Column(name="project_type", type="integer", nullable=false) */
    private int $type = self::PROJECT_TYPE_UNDEFINED;
    /** @ORM\Column(name="sum_type", type="integer", nullable=false) */
    private int $sumType = self::PROJECT_HOURS_SUM_MONTHLY;
    /** @ORM\Column(name="planned_budget", type="integer", nullable=true) */
    private ?int $plannedBudget = null;
    /** @ORM\Column(name="archived", type="boolean", nullable=false, options={"default" : 0}) */
    private bool $archived = false;
    /** @ORM\Column(name="billable", type="boolean", nullable=false) */
    private bool $billable = false;
    /** @ORM\Column(name="code", type="string", nullable=true) */
    private ?string $code = null;

    /**
     * @Assert\NotBlank @Assert\Length(min=2, max=50)
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     */
    private string $name;

    /**
     * @var Collection<int,DataProcessingHistory>
     * @ORM\OneToMany(targetEntity="DataProcessingHistory", mappedBy="project")
     */
    private Collection $dataProcessingHistory;

    /**
     * @var Collection<int,DataProcessingCriteria>
     * @ORM\ManyToMany(targetEntity="DataProcessingCriteria", inversedBy="projects")
     * @ORM\JoinTable(name="project_data_processing_criterium",
     *      joinColumns={@JoinColumn(name="project_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={
     *          @JoinColumn(name="data_processing_criterium_id", referencedColumnName="id", onDelete="CASCADE")
     *      }
     * )
     */
    private Collection $criteria;

    /**
     * @var Collection<int,GitlabProject>
     * @ORM\ManyToMany(targetEntity="Divante\Bundle\AdventureBundle\Entity\GitlabProject", mappedBy="projects")
     */
    private Collection $gitlabProjects;

    public function __construct()
    {
        $this->criteria = new ArrayCollection();
        $this->dataProcessingHistory = new ArrayCollection();
        $this->gitlabProjects = new ArrayCollection();
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

    public function getDescription() : ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description) : self
    {
        $this->description = $description;
        return $this;
    }

    public function getStartedAt() : ?string
    {
        return $this->startedAt;
    }

    public function getStartedAtTimestamp() : int
    {
        $startedAt = $this->getStartedAt();
        if (is_null($startedAt)) {
            return -1;
        }
        return \DateTime::createFromFormat("Y-m-d", $startedAt)->getTimestamp();
    }

    public function setStartedAt(?string $startedAt) : self
    {
        $this->startedAt = $startedAt;
        return $this;
    }

    public function getEndedAt() : ?string
    {
        return $this->endedAt;
    }

    public function getEndedAtTimestamp() : int
    {
        $endedAt = $this->getEndedAt();
        if (is_null($endedAt)) {
            return -1;
        }
        return \DateTime::createFromFormat("Y-m-d", $endedAt)->getTimestamp();
    }

    public function setEndedAt(?string $endedAt) : self
    {
        $this->endedAt = $endedAt;
        return $this;
    }

    public function getSumType() : int
    {
        return $this->sumType;
    }

    public function setSumType(int $sumType) : self
    {
        $this->sumType = $sumType;
        return $this;
    }

    public function getPlannedBudget() : ?int
    {
        return $this->plannedBudget;
    }

    public function setPlannedBudget(?int $plannedBudget) : self
    {
        $this->plannedBudget = $plannedBudget;
        return $this;
    }
    
    public function __toString() : string
    {
        return $this->name;
    }

    /** @return Collection<int,DataProcessingCriteria> */
    public function getCriteria() : Collection
    {
        return $this->criteria;
    }

    public function addCriteria(DataProcessingCriteria $criterion) : self
    {
        $this->criteria[] = $criterion;
        return $this;
    }

    /** @return Collection<int,DataProcessingHistory> */
    public function getDataProcessingHistory() : Collection
    {
        return $this->dataProcessingHistory;
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

    public function isArchived(): bool
    {
        return $this->archived;
    }

    public function setArchived(bool $archived): self
    {
        $this->archived = $archived;
        return $this;
    }

    public function isBillable(): bool
    {
        return $this->billable;
    }

    public function setBillable(bool $billable): self
    {
        $this->billable = $billable;
        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    /** @return Collection<int,GitlabProject> */
    public function getGitlabProjects() : Collection
    {
        return $this->gitlabProjects;
    }

    public function addGitlabProject(GitlabProject $gitlabProject) : self
    {
        if (!$this->gitlabProjects->contains($gitlabProject)) {
            $this->gitlabProjects->add($gitlabProject);
            $gitlabProject->addProject($this);
        }
        return $this;
    }

    public function removeGitlabProject(GitlabProject $gitlabProject) : self
    {
        if ($this->gitlabProjects->contains($gitlabProject)) {
            $this->gitlabProjects->removeElement($gitlabProject);
            $gitlabProject->removeProject($this);
        }
        return $this;
    }

    public function clearGitlabProjects() : self
    {
        if ($this->gitlabProjects->count() > 0) {
            /** @var GitlabProject $gitlabProject $gitlabProject */
            foreach ($this->gitlabProjects->toArray() as $gitlabProject) {
                $gitlabProject->removeProject($this);
            }
            $this->gitlabProjects->clear();
        }
        return $this;
    }

    public function getSlackType(): string
    {
        return 'AdventureBundle:Project';
    }

    public function getSlackLanguage(): string
    {
        return 'en';
    }
}
