<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 31.01.19
 * Time: 14:16
 */

namespace Divante\Bundle\AdventureBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class EvidenceOvertime
 * @package Divante\Bundle\AdventureBundle\Entity
 * @ORM\Entity()
 * @ORM\Table(name="evidence_overtime")
 */
class EvidenceOvertime
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var Evidence
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Evidence", inversedBy="overtimeEntries")
     * @ORM\JoinColumn(name="evidence_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $evidence;

    /**
     * @var string
     * @ORM\Column(name="project_name", type="string")
     */
    private $projectName;

    /**
     * @var string
     * @ORM\Column(name="project_code", type="string")
     */
    private $projectCode;

    /**
     * @var string
     * @ORM\Column(name="hours", type="decimal", precision=5, scale=2)
     */
    private $hours;

    /**
     * @var int
     * @ORM\Column(name="percentage", type="smallint")
     */
    private $percentage;

    /**
     * @var string
     * @ORM\Column(name="time_info", type="string")
     */
    private $timeInfo;

    public function getId() : int
    {
        return $this->id;
    }

    public function getEvidence() : Evidence
    {
        return $this->evidence;
    }

    public function setEvidence(Evidence $evidence) : self
    {
        $this->evidence = $evidence;
        return $this;
    }

    public function getProjectName() : string
    {
        return $this->projectName;
    }

    public function setProjectName(string $name) : self
    {
        $this->projectName = $name;
        return $this;
    }

    public function getProjectCode() : string
    {
        return $this->projectCode;
    }

    public function setProjectCode(string $code) : self
    {
        $this->projectCode = $code;
        return $this;
    }

    public function getHours() : string
    {
        return $this->hours;
    }

    public function setHours(string $hours) : self
    {
        $this->hours = $hours;
        return $this;
    }

    public function getPercentage() : int
    {
        return $this->percentage;
    }

    public function setPercentage(int $percentage) : self
    {
        $this->percentage = $percentage;
        return $this;
    }

    public function getTimeInfo() : string
    {
        return $this->timeInfo;
    }

    public function setTimeInfo(string $timeInfo) : self
    {
        $this->timeInfo = $timeInfo;
        return $this;
    }
}
