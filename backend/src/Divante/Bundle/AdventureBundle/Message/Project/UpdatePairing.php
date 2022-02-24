<?php
/**
 * Created by PhpStorm.
 * User: pawel
 * Date: 01.02.19
 * Time: 13:47
 */

namespace Divante\Bundle\AdventureBundle\Message\Project;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class UpdatePairing
{
    use ObjectTrait;

    private int $id;
    private bool $overtime;
    private int $employeeId;
    private int $projectId;
    /** @var string[] */
    private array $dateFrom;
    /** @var string[] */
    private array $dateTo;

    /**
     * UpdatePairing constructor.
     * @param int $id
     * @param string[] $dateFrom
     * @param string[] $dateTo
     * @param bool $overtime
     * @param int $employeeId
     * @param int $projectId
     */
    public function __construct(
        int $id,
        array $dateFrom,
        array $dateTo,
        bool $overtime,
        int $employeeId,
        int $projectId
    ) {
        $this->id = $id;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->overtime = $overtime;
        $this->employeeId = $employeeId;
        $this->projectId = $projectId;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmployeeId(): int
    {
        return $this->employeeId;
    }

    public function getProjectId(): int
    {
        return $this->projectId;
    }

    public function getOvertime(): bool
    {
        return $this->overtime;
    }

    /** @return string[] */
    public function getDatesFrom(): array
    {
        return $this->dateFrom;
    }

    /** @return string[] */
    public function getDatesTo(): array
    {
        return $this->dateTo;
    }
}
