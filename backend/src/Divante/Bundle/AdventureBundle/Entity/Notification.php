<?php

namespace Divante\Bundle\AdventureBundle\Entity;

use Divante\Bundle\AdventureBundle\Entity\Data\Id;
use Doctrine\ORM\Mapping as ORM;

/**
 * Notification
 *
 * @package Divante\Bundle\AdventureBundle\Entity
 * @ORM\Entity
 * @ORM\Table(name="notification")
 */
class Notification
{
    use Id;

    const PROJECT_PATH = '/firm/projects/';
    const TRIBE_PATH = '/firm/tribes/';
    const FREE_DAYS_PATH = '/free-days';
    const REQUESTS_PATH = '/requests';

    const USER_TRIBE_ADD = 'You have been added to tribe';
    const USER_TRIBE_DELETE = 'You have been deleted from tribe';
    const USER_PROJECTS_DELETED = 'All yours periods have been deleted from project';
    const USER_PROJECT_DELETED = 'One period have been deleted from your project';
    const USER_PROJECT_EDITED = 'Period has been edited in your project';
    const USER_PROJECT_NEW = 'You have been given a new period in the project';
    const USER_REQUEST_ACCEPTED = 'Your request is accepted ID:';
    const USER_REQUEST_REJECTED = 'Your request is rejected ID:';
    const USER_RESIGNATION_REQUEST_ACCEPTED = 'Your resignation request is accepted ID:';
    const USER_RESIGNATION_REQUEST_REJECTED = 'Your resignation request is rejected ID:';
    const MANAGER_NEW_REQUEST = 'You have outstanding request to manage for';
    const HARDWARE_TO_GENERATE = 'You have outstanding hardware lending agreement to generate for';
    const HARDWARE_TO_SIGN_FOR = 'You have outstanding hardware lending agreement to sign for';
    const HARDWARE_TO_SIGN = 'You have outstanding hardware lending agreement to sign';
    const MANAGER_NEW_EVIDENCE = 'You have outstanding evidence to manage for';
    const MANAGER_RESIGN_RESIGNATION = 'Person has resign from resignation request ID:';
    const MANAGER_RESIGN = 'Person has resign from request ID:';
    const MANAGER_PLANNED_RESIGN = 'Person has resign from planned request ID:';
    const MANAGER_NEW_RESIGN = 'You have outstanding resign request to manage for';
    const MANAGER_NEW_PLANNED_REQUEST = 'You have outstanding planned request to accept for:';

    /**
     * Employee id
     *
     * @var integer
     *
     * @ORM\Column(name="employee_id", type="integer", nullable=false)
     */
    private $employeeId;

    /**
     * Notification description
     *
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=150, nullable=false)
     */
    private $description;

    /**
     * Subject - f.ex project name, tribe name, person who is waiting for approval request etc.
     *
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=150, nullable=true)
     */
    private $subject;

    /**
     * Path to site connected with notification
     *
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=50, nullable=true)
     */
    private $path;

    /**
     * @ORM\Column(name="bold", type="boolean", length=50, nullable=true)
     */
    private ?bool $bold;

    public function getEmployeeId(): int
    {
        return $this->employeeId;
    }

    public function setEmployeeId(int $employeeId): Notification
    {
        $this->employeeId = $employeeId;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): Notification
    {
        $this->description = $description;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): Notification
    {
        $this->subject = $subject;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): Notification
    {
        $this->path = $path;

        return $this;
    }

    public function getBold(): bool
    {
        return $this->bold ?? false;
    }

    public function setBold(bool $bold): Notification
    {
        $this->bold = $bold;
        return $this;
    }
}
