<?php

namespace Divante\Bundle\AdventureBundle\Entity\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Checklist
 * @package Divante\Bundle\AdventureBundle\Entity\Checklist
 * @ORM\Entity(repositoryClass="Divante\Bundle\AdventureBundle\Entity\Repository\Checklist\ChecklistRepository")
 * @ORM\Table(name="checklist")
 */
class Checklist implements ChecklistInterface
{
    use ChecklistTrait;

    /** @ORM\Column(name="started_at", type="datetime") */
    private \DateTime $startedAt;
    /** @ORM\Column(name="finished_at", type="datetime", nullable=true) */
    private ?\DateTime $finishedAt = null;
    /**
     * @ORM\OneToMany(targetEntity="ChecklistQuestion", mappedBy="checklist", cascade={"remove"})
     * @var Collection<int,ChecklistQuestion>
     */
    private Collection $questions;
    /**
     * @var Collection<int,Employee>
     * @ORM\ManyToMany(targetEntity="Divante\Bundle\AdventureBundle\Entity\Employee")
     * @ORM\JoinTable(name="checklist_owner",
     *      joinColumns={@ORM\JoinColumn(name="checklist_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="employee_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private Collection $owners;
    /**
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Employee")
     * @ORM\JoinColumn(name="subject_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private Employee $subject;
    /** @ORM\Column(name="hidden", type="boolean") */
    private bool $hidden = false;
    /** @ORM\Column(name="due_date", type="datetime") */
    private \DateTime $dueDate;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->owners = new ArrayCollection();
    }

    public function getStartedAt(): \DateTime
    {
        return $this->startedAt;
    }

    public function setStartedAt(\DateTime $startedAt): self
    {
        $this->startedAt = $startedAt;
        return $this;
    }

    public function getFinishedAt(): ?\DateTime
    {
        return $this->finishedAt;
    }

    public function setFinishedAt(?\DateTime $finishedAt): self
    {
        $this->finishedAt = $finishedAt;
        return $this;
    }

    public function getQuestions() : Collection
    {
        /** @var Collection<int,ChecklistQuestionInterface> $result */
        $result = isset($this->questions) ? $this->questions : new ArrayCollection();
        return $result;
    }

    public function addQuestion(ChecklistQuestion $question) : self
    {
        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
            $question->setChecklist($this);
        }
        return $this;
    }

    public function getOwners(): Collection
    {
        return $this->owners;
    }

    /**
     * @param array<Employee> $owners
     *
     * @return $this
     */
    public function setOwners(array $owners): self
    {
        $this->owners = new ArrayCollection($owners);
        return $this;
    }
    public function getSubject(): Employee
    {
        return $this->subject;
    }

    public function setSubject(Employee $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    public function isHidden(): bool
    {
        return $this->hidden;
    }

    public function setHidden(bool $hidden): self
    {
        $this->hidden = $hidden;
        return $this;
    }

    public function getDueDate(): \DateTime
    {
        return $this->dueDate;
    }

    public function setDueDate(\DateTime $dueDate): self
    {
        $this->dueDate = $dueDate;
        return $this;
    }
}
