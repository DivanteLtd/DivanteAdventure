<?php

namespace Divante\Bundle\AdventureBundle\Entity\Checklist;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ChecklistTemplateQuestion
 * @package Divante\Bundle\AdventureBundle\Entity\Checklist
 * @ORM\Entity @ORM\Table(name="checklist_template_question")
 * @ORM\MappedSuperclass
 */
class ChecklistTemplateQuestion extends AbstractChecklistQuestion implements ChecklistQuestionInterface
{
    /**
     * @ORM\ManyToOne(targetEntity="ChecklistTemplate", inversedBy="questions")
     * @ORM\JoinColumn(name="checklist_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private ChecklistTemplate $checklist;

    public function getChecklist(): ChecklistTemplate
    {
        return $this->checklist;
    }

    public function setChecklist(ChecklistTemplate $checklist): self
    {
        $this->checklist = $checklist;
        return $this;
    }
}
