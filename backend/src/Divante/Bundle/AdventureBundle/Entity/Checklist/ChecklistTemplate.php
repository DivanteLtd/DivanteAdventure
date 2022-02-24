<?php

namespace Divante\Bundle\AdventureBundle\Entity\Checklist;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class ChecklistTemplate
 * @package Divante\Bundle\AdventureBundle\Entity\Checklist
 * @ORM\Entity @ORM\Table(name="checklist_template")
 */
class ChecklistTemplate implements ChecklistInterface
{
    use ChecklistTrait;

    /**
     * @ORM\OneToMany(targetEntity="ChecklistTemplateQuestion", mappedBy="checklist", cascade={"remove"})
     * @var Collection<int,ChecklistTemplateQuestion>
     */
    private Collection $questions;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
    }

    public function getQuestions() : Collection
    {
        /** @var Collection<int,ChecklistQuestionInterface> $result */
        $result = isset($this->questions) ? $this->questions : new ArrayCollection();
        return $result;
    }

    public function addQuestion(ChecklistTemplateQuestion $question) : self
    {
        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
            $question->setChecklist($this);
        }
        return $this;
    }
}
