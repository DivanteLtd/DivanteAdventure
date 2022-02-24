<?php

namespace Divante\Bundle\AdventureBundle\Entity\Feedback;

use DateTime;
use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Divante\Bundle\AdventureBundle\Entity\Data\Timestampable;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class PlannedFeedback
 * @package Divante\Bundle\AdventureBundle\Entity\Feedback
 * @ORM\Entity
 * @ORM\Table(name="planned_feedback")
 */
class PlannedFeedback
{
    use Timestampable, Id;

    /**
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Employee")
     * @ORM\JoinColumn(name="employee_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private Employee $employee;

    /**
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Employee")
     * @ORM\JoinColumn(name="leader_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private Employee $leader;

    /** @ORM\Column(name="date", type="date", nullable=false) */
    private DateTime $date;

    public function setEmployee(Employee $employee): PlannedFeedback
    {
        $this->employee = $employee;
        return $this;
    }

    public function getEmployee(): Employee
    {
        return $this->employee;
    }

    public function setLeader(Employee $leader): PlannedFeedback
    {
        $this->leader = $leader;
        return $this;
    }

    public function getLeader(): Employee
    {
        return $this->leader;
    }

    public function setDate(DateTime $date): PlannedFeedback
    {
        $this->date = $date;
        return $this;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }
}
