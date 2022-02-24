<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 */

namespace Divante\Bundle\AdventureBundle\Entity;

use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class EmployeeWorkLocation
 *
 * @ORM\Table(name="employee_work_location")
 * @ORM\Entity(repositoryClass="Divante\Bundle\AdventureBundle\Entity\Repository\WorkLocation\WorkLocationRepository")
 */
class EmployeeWorkLocation
{
    use Id;

    public const TYPE_REMOTE = 1;
    public const TYPE_DELEGATION = 2;

    /** @ORM\Column(name="employee_id", type="integer", nullable=false) */
    private int $employeeId;

    /**
     * Type of work location 1 - work remotely, 2 - delegation
     * @ORM\Column(name="type", type="integer", nullable=false)
     */
    private int $type;

    /** @ORM\Column(name="date", type="date", nullable=false) */
    private \Datetime $date;

    public function getEmployeeId(): int
    {
        return $this->employeeId;
    }

    public function setEmployeeId(int $employeeId): self
    {
        $this->employeeId = $employeeId;

        return $this;
    }

    public function getType() : int
    {
        return $this->type;
    }

    public function setType(int $type) : self
    {
        $this->type = $type;

        return $this;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }
}
