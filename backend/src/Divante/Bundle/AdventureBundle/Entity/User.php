<?php
namespace Divante\Bundle\AdventureBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Divante\Bundle\AdventureBundle\Entity\Repository\UserRepository")
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    // Remember to update security.yml
    public const ROLES_LEVELS = [
        'ROLE_USER' => [],
        'ROLE_HR' => [ 'ROLE_USER' ],
        'ROLE_MANAGER' => [ 'ROLE_USER' ],
        'ROLE_TRIBE_MASTER' => [ 'ROLE_HR', 'ROLE_MANAGER' ],
        'ROLE_HELPDESK' => [ 'ROLE_USER' ],
        'ROLE_SUPER_ADMIN' => [ 'ROLE_TRIBE_MASTER', 'ROLE_HELPDESK' ],
    ];
    /**
     * @var string
     */
    public const MAX_ERROR_COUNT = 3;

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="google_id", type="string", nullable=true)
     */
    private $googleId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="login_expiration", type="datetime", nullable=true)
     */
    private $loginExpiration;

    /**
     * @var int
     *
     * @ORM\Column(name="login_errors", type="smallint", nullable=false)
     */
    private $loginErrors = 0;

    /**
     * @var Employee|null
     * @ORM\OneToOne(targetEntity="Divante\Bundle\AdventureBundle\Entity\Employee", inversedBy="fosUser")
     * @ORM\JoinColumn(name="employee_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $employee;

    /**
     * @var string|null
     *
     * @Orm\Column(name="locked", type="string", nullable=true)
     */
    private $locked;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function setGoogleId(?string $googleId) : self
    {
        $this->googleId = $googleId;
        return $this;
    }

    public function getGoogleId() : ?string
    {
        return $this->googleId;
    }

    public function getEmployee() : Employee
    {
        return $this->employee;
    }

    public function setEmployee(Employee $employee) : self
    {
        $this->employee = $employee;
        return $this;
    }

    public function getEmployeeId() : ?int
    {
        if (is_null($this->employee)) {
            return null;
        }
        return $this->employee->getId();
    }

    /**
     * @param \DateTime $loginExpiration
     *
     * @return User
     */
    public function setLoginExpiration(?\DateTime $loginExpiration): User
    {
        $this->loginExpiration = $loginExpiration;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLoginExpiration(): ?\DateTime
    {
        return $this->loginExpiration;
    }

    /**
     * @param int $loginErrors
     *
     * @return User
     */
    public function setLoginErrors(int $loginErrors): User
    {
        $this->loginErrors = $loginErrors;

        return $this;
    }

    /**
     * @return int
     */
    public function getLoginErrors(): int
    {
        return $this->loginErrors;
    }

    /**
     * @return bool
     */
    public function getEnabled()
    {
        return (bool) $this->enabled;
    }

    /**
     * @return bool
     */
    public function getIsLocked(): bool
    {
        return (bool) $this->locked;
    }

    /**
     * @return null|string
     */
    public function getLocked()
    {
        return $this->locked;
    }

    public function setLocked(?string $locked) : self
    {
        $this->locked = $locked;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getRoles() : array
    {
        $rolesQueue = parent::getRoles();
        $allRoles = [ 'ROLE_USER' ];
        while (count($rolesQueue) > 0) {
            $role = array_pop($rolesQueue);
            if (!in_array($role, $allRoles)) {
                $allRoles[] = $role;
            }
            $rolesQueue = array_merge($rolesQueue, self::ROLES_LEVELS[$role] ?? []);
        }
        return $allRoles;
    }
}
