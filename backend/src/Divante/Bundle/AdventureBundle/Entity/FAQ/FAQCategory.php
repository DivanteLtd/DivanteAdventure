<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\Entity\FAQ;

use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Divante\Bundle\AdventureBundle\Entity\Data\Timestampable;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class FAQCategory
 * @package Divante\Bundle\AdventureBundle\Entity\FAQ
 * @ORM\Entity
 * @ORM\Table(name="faq_category")
 */
class FAQCategory
{
    use Timestampable, Id;

    /**
     * @var Collection<int,Employee>
     * @ORM\ManyToMany(targetEntity="Divante\Bundle\AdventureBundle\Entity\Employee", inversedBy="fAQCategory")
     * @ORM\JoinTable(name="faq_category_responsible",
     *     joinColumns={@ORM\JoinColumn(name="faqcategory_id", referencedColumnName="id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="employee_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    private Collection $employee;
    /** @ORM\Column(name="name_pl", type="string", length=50, nullable=false) */
    private string $namePl;
    /** @ORM\Column(name="name_en", type="string", length=50, nullable=false) */
    private string $nameEn;

    public function __construct()
    {
        $this->employee = new ArrayCollection();
    }

    /** @return Collection<int,Employee> */
    public function getEmployee() : Collection
    {
        return $this->employee;
    }

    public function getNamePl() : string
    {
        return $this->namePl;
    }

    public function setNamePl(string $namePl) : self
    {
        $this->namePl = $namePl;
        return $this;
    }

    public function getNameEn() : string
    {
        return $this->nameEn;
    }

    public function setNameEn(string $nameEn) : self
    {
        $this->nameEn = $nameEn;
        return $this;
    }
}
