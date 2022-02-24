<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 04.01.19
 * Time: 08:37
 */

namespace Divante\Bundle\AdventureBundle\MessageHandler;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Project;
use Divante\Bundle\AdventureBundle\Message\CreateOccupancyEntry;
use Doctrine\ORM\EntityManagerInterface;

class CreateOccupancyEntryHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param CreateOccupancyEntry $message
     * @throws \Exception
     */
    public function __invoke(CreateOccupancyEntry $message) : void
    {
        $em = $this->em;

        $employeeId = $message->getEmployeeId();
        $projectId = $message->getProjectId();
        $timestamp = $message->getTimestamp();
        $occupancy = $message->getOccupancy();
        if ($employeeId === -1) {
            throw new \Exception("employeeId parameter is required");
        }
        if ($projectId === -1) {
            throw new \Exception("projectId parameter is required");
        }
        if ($timestamp === -1) {
            throw new \Exception("timestamp parameter is required");
        }
        if ($occupancy === -1) {
            throw new \Exception("secondsPerDay parameter is required");
        }

        $employee = $em->getRepository(Employee::class)->find($employeeId);
        $project = $em->getRepository(Project::class)->find($projectId);

        if (is_null($employee)) {
            throw new \Exception("Employee with id $employeeId not found.");
        }
        if (is_null($project)) {
            throw new \Exception("Project with id $projectId not found.");
        }

        $em->getConnection()->beginTransaction();
        try {
            $em->getConnection()->prepare(
                "INSERT INTO employee_occupancy SET 
                    employee_id = \"$employeeId\",
                    project_id = \"$projectId\",
                    date = \"$timestamp\",
                    occupancy = \"$occupancy\" 
                ON DUPLICATE KEY UPDATE occupancy = \"$occupancy\"
            "
            )->execute();
            $em->getConnection()->commit();
        } catch (\Exception $e) {
            $em->getConnection()->rollBack();
            throw new \Exception("Creating employee occupancy entry failed", 0, $e);
        }
    }
}
