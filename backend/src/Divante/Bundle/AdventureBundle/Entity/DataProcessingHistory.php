<?php
/**
 * @package  Divante\Bundle
 * @author BH <bh@divante.pl>
 * @copyright 2017 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\Bundle\AdventureBundle\Entity;

use DateTime;
use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class DataProcessingHistory
 *
 * @ORM\Table(name="data_processing_history")
 * @ORM\Entity
 */
class DataProcessingHistory
{
    use Id;
    /**
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="dataProcessingHistory")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private Project $project;

    /** @ORM\Column(name="first_name", type="string", nullable=false) */
    private string $firstName;
    /** @ORM\Column(name="last_name", type="string", nullable=false) */
    private string $lastName;
    /** @ORM\Column(name="started_at", type="datetime", nullable=false) */
    private DateTime $startedAt;
    /** @ORM\Column(name="ended_at", type="datetime", nullable=true) */
    private ?DateTime $endedAt = null;

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): DataProcessingHistory
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): DataProcessingHistory
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getStartedAt(): DateTime
    {
        return $this->startedAt;
    }

    public function setStartedAt(DateTime $startedAt): DataProcessingHistory
    {
        $this->startedAt = $startedAt;
        return $this;
    }

    public function getEndedAt(): ?DateTime
    {
        return $this->endedAt;
    }

    public function setEndedAt(?DateTime $endedAt): DataProcessingHistory
    {
        $this->endedAt = $endedAt;
        return $this;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function setProject(Project $project): DataProcessingHistory
    {
        $this->project = $project;
        return $this;
    }
}
