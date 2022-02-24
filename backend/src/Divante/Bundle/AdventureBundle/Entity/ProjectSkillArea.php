<?php

namespace Divante\Bundle\AdventureBundle\Entity;

use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Divante\Bundle\AdventureBundle\Entity\Data\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ProjectSkillArea
 *
 * @ORM\Table(
 *      name="project_skill_area",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="project_id_skill_area_id", columns={"project_id", "skill_area_id"})
 *      },
 *      indexes={
 *          @ORM\Index(name="FK_project_skill_area_skill_area", columns={"skill_area_id"}),
 *          @ORM\Index(name="IDX_8873DAE8166D1F9C", columns={"project_id"})
 *      }
 * )
 * @ORM\Entity
 */
class ProjectSkillArea
{
    use Timestampable, Id;

    /**
     * Project skill area required value
     * @var float|null
     * @ORM\Column(name="value_required", type="float", precision=10, scale=0, nullable=true)
     */
    private $valueRequired = 0;

    /**
     * Project skill area averaged value from required skills of employee in project
     * @var float|null
     * @ORM\Column(name="value_averaged", type="float", precision=10, scale=0, nullable=true)
     */
    private $valueAveraged = 0;

    /**
     * Skill area id
     * @var \Divante\Bundle\AdventureBundle\Entity\SkillArea
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\SkillArea")
     * @ORM\JoinColumn(name="skill_area_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $skillArea;

    /**
     * Project id
     * @var \Divante\Bundle\AdventureBundle\Entity\Project
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Project")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $project;

    public function getValueRequired() : ?float
    {
        return $this->valueRequired;
    }

    public function setValueRequired(?float $valueRequired) : self
    {
        $this->valueRequired = $valueRequired;
        return $this;
    }

    public function getValueAveraged() : ?float
    {
        return $this->valueAveraged;
    }

    public function setValueAveraged(?float $valueAveraged) : self
    {
        $this->valueAveraged = $valueAveraged;
        return $this;
    }

    public function getSkillArea() : SkillArea
    {
        return $this->skillArea;
    }

    public function setSkillArea(SkillArea $skillArea) : self
    {
        $this->skillArea = $skillArea;
        return $this;
    }

    public function getProject() : Project
    {
        return $this->project;
    }

    public function setProject(Project $project) : self
    {
        $this->project = $project;
        return $this;
    }
}
