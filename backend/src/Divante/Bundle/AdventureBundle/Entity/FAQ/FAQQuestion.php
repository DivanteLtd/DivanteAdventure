<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\Entity\FAQ;

use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Divante\Bundle\AdventureBundle\Entity\Data\Timestampable;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class FAQQuestion
 * @package Divante\Bundle\AdventureBundle\Entity\FAQ
 * @ORM\Entity
 * @ORM\Table(name="faq_question")
 */
class FAQQuestion
{
    use Timestampable, Id;

    /**
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Employee")
     * @ORM\JoinColumn(name="employee_id", onDelete="CASCADE", referencedColumnName="id", nullable=false)
     */
    private Employee $employee;
    /**
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\FAQ\FAQCategory")
     * @ORM\JoinColumn(name="faq_category_id", onDelete="CASCADE", referencedColumnName="id", nullable=false)
     */
    private FAQCategory $fAQCategory;
    /** @ORM\Column(name="question_pl", type="text", length=16777215, nullable=false, columnDefinition="mediumblob") */
    private string $questionPl;
    /** @ORM\Column(name="question_en", type="text", length=16777215, nullable=false, columnDefinition="mediumblob") */
    private string $questionEn;
    /** @ORM\Column(name="answer_pl", type="text", length=16777215, nullable=false, columnDefinition="mediumblob") */
    private string $answerPl;
    /** @ORM\Column(name="answer_en", type="text", length=16777215, nullable=false, columnDefinition="mediumblob") */
    private string $answerEn;

    public function getEmployee() : Employee
    {
        return $this->employee;
    }

    public function setEmployee(Employee $employee) : self
    {
        $this->employee = $employee;
        return $this;
    }

    public function getFAQCategory() : FAQCategory
    {
        return $this->fAQCategory;
    }

    public function setFAQCategory(FAQCategory $fAQCategory) : self
    {
        $this->fAQCategory = $fAQCategory;
        return $this;
    }

    public function getQuestionPl() : string
    {
        return $this->questionPl;
    }

    public function setQuestionPl(string $questionPl) : self
    {
        $this->questionPl = $questionPl;
        return $this;
    }

    public function getQuestionEn() : string
    {
        return $this->questionEn;
    }

    public function setQuestionEn(string $questionEn) : self
    {
        $this->questionEn = $questionEn;
        return $this;
    }

    public function getAnswerPl() : string
    {
        return $this->answerPl;
    }

    public function setAnswerPl(string $answerPl) : self
    {
        $this->answerPl = $answerPl;
        return $this;
    }

    public function getAnswerEn() : string
    {
        return $this->answerEn;
    }

    public function setAnswerEn(string $answerEn) : self
    {
        $this->answerEn = $answerEn;
        return $this;
    }
}
