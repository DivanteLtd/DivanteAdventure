<?php
/**
 * @package  Divante\Bundle
 * @author BH <bh@divante.pl>
 * @copyright 2017 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\Bundle\AdventureBundle\Entity;

use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DataProcessingCriteria
 *
 * @ORM\Table(name="data_processing_criterium")
 * @ORM\Entity()
 */
class DataProcessingCriteria
{
    use Id;

    /** @ORM\Column(name="name_pl", type="string", length=80, nullable=false) */
    private string $namePl;
    /** @ORM\Column(name="name_en", type="string", length=80, nullable=false) */
    private string $nameEn;

    /**
     * @var Collection<int,Project>
     * @ORM\ManyToMany(targetEntity="Project", mappedBy="criteria")
     */
    private Collection $projects;

    public function __construct()
    {
        $this->projects = new ArrayCollection();
    }

    public function setNamePl(string $namePl): self
    {
        $this->namePl = $namePl;
        return $this;
    }

    public function getNamePl(): string
    {
        return (string) $this->namePl;
    }

    public function setNameEn(string $nameEn): self
    {
        $this->nameEn = $nameEn;

        return $this;
    }

    public function getNameEn(): string
    {
        return (string) $this->nameEn;
    }

    /** @return Collection<int,Project> */
    public function getProjects() : Collection
    {
        return $this->projects;
    }
}
