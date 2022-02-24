<?php

namespace Divante\Bundle\AdventureBundle\Entity\Checklist;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ChecklistQuestion
 * @package Divante\Bundle\AdventureBundle\Entity\Checklist
 * @ORM\Entity @ORM\Table(name="checklist_question")
 * @ORM\MappedSuperclass
 */
class ChecklistQuestion extends AbstractChecklistQuestion implements ChecklistQuestionInterface
{
    /** @ORM\Column(name="current_status", type="integer") */
    private int $currentStatus;
    /** @ORM\Column(name="checked_at", type="datetime", nullable=true) */
    private ?\DateTime $checkedAt = null;
    /** @ORM\Column(name="last_ping_date", type="date", nullable=true) */
    private ?\DateTime $lastPingDate = null;
    /**
     * @ORM\ManyToOne(targetEntity="Checklist", inversedBy="questions")
     * @ORM\JoinColumn(name="checklist_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private Checklist $checklist;

    public function getCurrentStatus(): int
    {
        return $this->currentStatus;
    }

    public function setCurrentStatus(int $currentStatus): self
    {
        $this->currentStatus = $currentStatus;
        return $this;
    }

    public function getCheckedAt(): ?\DateTime
    {
        return $this->checkedAt;
    }

    public function setCheckedAt(?\DateTime $checkedAt): self
    {
        $this->checkedAt = $checkedAt;
        return $this;
    }

    public function getLastPingDate(): ?\DateTime
    {
        return $this->lastPingDate;
    }

    public function setLastPingDate(?\DateTime $lastPingDate): self
    {
        $this->lastPingDate = $lastPingDate;
        return $this;
    }

    public function getChecklist(): Checklist
    {
        return $this->checklist;
    }

    public function setChecklist(Checklist $checklist): self
    {
        $this->checklist = $checklist;
        return $this;
    }
}
