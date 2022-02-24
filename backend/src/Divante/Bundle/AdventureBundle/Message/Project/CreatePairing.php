<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 02.01.19
 * Time: 10:22
 */

namespace Divante\Bundle\AdventureBundle\Message\Project;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class CreatePairing
{
    use ObjectTrait;

    private int $employeeId;
    private int $projectId;
    private bool $overtime;
    /** @var string[] */
    private array $dateFrom;
    /** @var string[] */
    private array $dateTo;

    /**
     * CreatePairing constructor.
     * @param int $employeeId
     * @param int $projectId
     * @param string[] $dateFrom
     * @param string[] $dateTo
     * @param bool $overtime
     */
    public function __construct(int $employeeId, int $projectId, array $dateFrom, array $dateTo, bool $overtime)
    {
        $this->employeeId = $employeeId;
        $this->projectId = $projectId;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->overtime = $overtime;
    }

    public function getEmployeeId(): int
    {
        return $this->employeeId;
    }

    public function getProjectId(): int
    {
        return $this->projectId;
    }

    /** @return string[] */
    public function getDateFrom(): array
    {
        return $this->dateFrom;
    }

    /** @return string[] */
    public function getDateTo(): array
    {
        return $this->dateTo;
    }

    public function isOvertime(): bool
    {
        return $this->overtime;
    }
}
