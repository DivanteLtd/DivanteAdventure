<?php

namespace Divante\Bundle\AdventureBundle\Entity;

use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Divante\Bundle\AdventureBundle\Entity\Data\IntegrationProjectEntity;
use Divante\Bundle\AdventureBundle\Entity\Data\Timestampable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class GitlabProject
 * @package Divante\Bundle\AdventureBundle\Entity
 * @ORM\Entity
 * @ORM\Table(name="gitlab_project")
 */
class GitlabProject implements IntegrationProjectEntity
{
    use Id, Timestampable;

    public const GITLAB_TYPE_REPOSITORY = 0;
    public const GITLAB_TYPE_GROUP = 1;

    /** @ORM\Column(name="gitlab_id", type="integer", nullable=false) */
    private int $gitlabId;
    /** @ORM\Column(name="gitlab_type", type="integer", nullable=false) */
    private int $gitlabType;
    /**  @ORM\Column(name="name", type="string", nullable=false) */
    private string $name;

    /**
     * @var Collection<int,Project>
     * @ORM\ManyToMany(targetEntity="Divante\Bundle\AdventureBundle\Entity\Project", inversedBy="gitlabProjects")
     * @ORM\JoinTable(name="gitlab_project_mapping",
     *   joinColumns={@ORM\JoinColumn(name="gitlab_project_id", referencedColumnName="id", onDelete="CASCADE")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="project_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    private Collection $projects;

    public function __construct()
    {
        $this->projects = new ArrayCollection();
    }

    public function getGitlabId() : int
    {
        return $this->gitlabId;
    }

    public function setGitlabId(int $gitlabId) : self
    {
        $this->gitlabId = $gitlabId;
        return $this;
    }

    public function getGitlabType() : int
    {
        return $this->gitlabType;
    }

    public function setGitlabType(int $gitlabType) : self
    {
        $this->gitlabType = $gitlabType;
        return $this;
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

    /** @return Collection<int,Project> */
    public function getProjects() : Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project) : self
    {
        if (!$this->projects->contains($project)) {
            $this->projects->add($project);
            $project->addGitlabProject($this);
        }
        return $this;
    }

    public function removeProject(Project $project) : self
    {
        if ($this->projects->contains($project)) {
            $this->projects->removeElement($project);
            $project->removeGitlabProject($this);
        }
        return $this;
    }

    public function clearProjects() : self
    {
        if ($this->projects->count() > 0) {
            /** @var Project $project */
            foreach ($this->projects->toArray() as $project) {
                $project->removeGitlabProject($this);
            }
            $this->projects->clear();
        }
        return $this;
    }
}
