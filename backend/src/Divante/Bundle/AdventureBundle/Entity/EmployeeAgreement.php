<?php

namespace Divante\Bundle\AdventureBundle\Entity;

use Divante\Bundle\AdventureBundle\Entity\Data\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EmployeeAgreement
 *
 * @ORM\Table(
 *      name="employee_agreement",
 *      indexes={
 *          @ORM\Index(name="FK_employee_agreement_employee", columns={"employee_id"}),
 *          @ORM\Index(name="FK_employee_agreement_agreement", columns={"agreement_id"})
 *      }
 * )
 * @ORM\Entity
 */
class EmployeeAgreement
{
    use Timestampable;

    /**
     * Employee agreement id
     *
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * Employee
     *
     * @var \Divante\Bundle\AdventureBundle\Entity\Employee
     *
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Employee", inversedBy="agreements")
     * @ORM\JoinColumn(name="employee_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $employee;


    /**
     * @var string
     * @Assert\NotBlank @Assert\Email
     * @ORM\Column(name="email", type="string", length=254, nullable=true)
     */
    private $email;

    /**
     * @var string
     * @Assert\NotBlank @Assert\Length(min=2, max=150)
     * @ORM\Column(name="name", type="string", length=150, nullable=true)
     */
    private $name;

    /**
     * @var string
     * @Assert\NotBlank @Assert\Length(min=2, max=150)
     * @ORM\Column(name="lastName", type="string", length=150, nullable=true)
     */
    private $lastName;

    /**
     * Agreement
     *
     * @var \Divante\Bundle\AdventureBundle\Entity\Agreement
     *
     * @ORM\ManyToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Agreement")
     * @ORM\JoinColumn(name="agreement_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $agreement;

    /**
     * Get employee agreement id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set employee agreement id
     *
     * @param int $id
     *
     * @return EmployeeAgreement
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get employee
     *
     * @return Employee
     */
    public function getEmployee()
    {
        return $this->employee;
    }

    /**
     * Set employee
     *
     * @param Employee $employee
     *
     * @return EmployeeAgreement
     */
    public function setEmployee($employee)
    {
        $this->employee = $employee;
        return $this;
    }

    /**
     * Get agreement
     *
     * @return Agreement
     */
    public function getAgreement()
    {
        return $this->agreement;
    }

    /**
     * Set agreement
     *
     * @param Agreement $agreement
     *
     * @return EmployeeAgreement
     */
    public function setAgreement($agreement)
    {
        $this->agreement = $agreement;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return EmployeeAgreement
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return EmployeeAgreement
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * @return EmployeeAgreement
     */
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }
}
