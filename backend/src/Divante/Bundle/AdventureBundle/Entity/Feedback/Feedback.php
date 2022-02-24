<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\Entity\Feedback;

use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Divante\Bundle\AdventureBundle\Entity\Data\Timestampable;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Feedback
 * @package Divante\Bundle\AdventureBundle\Entity\Feedback
 * @ORM\Entity
 * @ORM\Table(name="feedback")
 */
class Feedback
{
    use Timestampable, Id;
    const TECH_FEEDBACK = 1;
    const FEEDBACK = 2;

    /**
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Employee")
     * @ORM\JoinColumn(name="employee_id", referencedColumnName="id", nullable=false)
     */
    private Employee $employee;

    /**
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Employee")
     * @ORM\JoinColumn(name="leader_id", referencedColumnName="id", nullable=false)
     */
    private Employee $leader;

    /** @ORM\Column(name="feedback", type="text", length=16777215, nullable=true, columnDefinition="mediumblob") */
    private ?string $feedback = null;
    /**
     * @ORM\Column(
     *     name="progress_feedback",
     *     type="text",
     *     length=16777215,
     *     nullable=true,
     *     columnDefinition="mediumblob"
     * )
     */
    private ?string $progressFeedback = null;
    /**
     * @ORM\Column(
     *     name="technical_feedback",
     *      type="text",
     *      length=16777215,
     *      nullable=true,
     *      columnDefinition="mediumblob"
     * )
     */
    private ?string $technicalFeedback = null;
    /** @ORM\Column(name="type", type="integer", nullable=false) */
    private int $type;

    /** @ORM\Column(name="date_created", type="datetime", nullable=true) */
    private ?\DateTime $dateCreated = null;

    public function getEmployee() : Employee
    {
        return $this->employee;
    }

    public function setEmployee(Employee $employee) : self
    {
        $this->employee = $employee;
        return $this;
    }

    public function getLeader() : Employee
    {
        return $this->leader;
    }

    public function setLeader(Employee $leader) : self
    {
        $this->leader = $leader;
        return $this;
    }

    public function getFeedback(): ?string
    {
        return $this->feedback;
    }

    public function setFeedback(?string $feedback): self
    {
        $this->feedback = $feedback;
        return $this;
    }

    public function getProgressFeedback(): ?string
    {
        return $this->progressFeedback;
    }

    public function setProgressFeedback(?string $progressFeedback): Feedback
    {
        $this->progressFeedback = $progressFeedback;
        return $this;
    }

    public function getTechnicalFeedback(): ?string
    {
        return $this->technicalFeedback;
    }

    public function setTechnicalFeedback(?string $technicalFeedback): Feedback
    {
        $this->technicalFeedback = $technicalFeedback;
        return $this;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function setDateCreated(?\DateTime $dateCreated): self
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    public function getDateCreated(): ?\DateTime
    {
        return $this->dateCreated;
    }
}
