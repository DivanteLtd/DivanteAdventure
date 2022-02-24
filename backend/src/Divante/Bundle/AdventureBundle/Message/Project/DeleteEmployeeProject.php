<?php
/**
 * Delete Employee Project
 *
 * @copyright  Copyright (c) 2015-2016 Divante Sp. z o.o. (http://divante.pl)
 */

namespace Divante\Bundle\AdventureBundle\Message\Project;

use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class DeleteEmployeeProject
{
    use ObjectTrait;

    private int $entryId;
    private int $employeeId;
    private int $projectId;

    public function __construct(int $id, int $employeeId, int $projectId)
    {
        $this->entryId = $id;
        $this->employeeId = $employeeId;
        $this->projectId = $projectId;
    }

    public function getEntryId(): int
    {
        return $this->entryId;
    }

    public function getEmployeeId(): int
    {
        return $this->employeeId;
    }

    public function getProjectId(): int
    {
        return $this->projectId;
    }
}
