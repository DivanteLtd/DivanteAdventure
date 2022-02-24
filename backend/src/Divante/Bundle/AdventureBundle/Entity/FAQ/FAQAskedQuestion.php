<?php

namespace Divante\Bundle\AdventureBundle\Entity\FAQ;

use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Divante\Bundle\AdventureBundle\Entity\Data\Timestampable;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class FAQAskedQuestion
 * @package Divante\Bundle\AdventureBundle\Entity\FAQ
 * @ORM\Entity
 * @ORM\Table(name="faq_asked_question")
 */
class FAQAskedQuestion
{
    use Timestampable, Id;

    /**
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Employee")
     * @ORM\JoinColumn(name="questioner_id", onDelete="CASCADE", referencedColumnName="id", nullable=false)
     */
    private Employee $questioner;
    /**
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\FAQ\FAQCategory")
     * @ORM\JoinColumn(name="faq_category_id", onDelete="CASCADE", referencedColumnName="id", nullable=false)
     */
    private FAQCategory $category;
    /** @ORM\Column(name="question", type="text", length=16777215, nullable=false, columnDefinition="mediumblob") */
    private string $question;
    /** @ORM\Column(name="language", type="text", length=2, nullable=true) */
    private ?string $language;

    public function getQuestioner(): Employee
    {
        return $this->questioner;
    }

    public function setQuestioner(Employee $questioner): self
    {
        $this->questioner = $questioner;
        return $this;
    }

    public function getCategory(): FAQCategory
    {
        return $this->category;
    }

    public function setCategory(FAQCategory $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getQuestion(): string
    {
        return $this->question;
    }

    public function setQuestion(string $question): self
    {
        $this->question = $question;
        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language): self
    {
        $this->language = $language;
        return $this;
    }
}
