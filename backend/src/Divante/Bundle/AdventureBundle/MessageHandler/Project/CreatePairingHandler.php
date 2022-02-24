<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 02.01.19
 * Time: 10:24
 */

namespace Divante\Bundle\AdventureBundle\MessageHandler\Project;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\EmployeeProject;
use Divante\Bundle\AdventureBundle\Entity\Project;
use Divante\Bundle\AdventureBundle\Events\Integration\AbstractEmployeeProjectAccessEvent;
use Divante\Bundle\AdventureBundle\Events\ProjectAssignEvent;
use Divante\Bundle\AdventureBundle\Message\Project\CreatePairing;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CreatePairingHandler
{
    private EntityManagerInterface $em;
    private EventDispatcherInterface $dispatcher;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $dispatcher)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param CreatePairing $message
     * @throws \Exception
     */
    public function __invoke(CreatePairing $message) : void
    {
        $employeeId = $message->getEmployeeId();
        $projectId = $message->getProjectId();
        $dateFrom = $message->getDateFrom();
        $dateTo = $message->getDateTo();
        if ($employeeId === -1) {
            throw new \Exception("employeeId parameter is required");
        }
        if ($projectId === -1) {
            throw new \Exception("projectId parameter is required");
        }

        $em = $this->em;
        /** @var Employee|null $employee */
        $employee = $em->getRepository(Employee::class)->find($employeeId);
        /** @var Project|null $project */
        $project = $em->getRepository(Project::class)->find($projectId);

        if (is_null($employee)) {
            throw new \Exception("Employee with id $employeeId not found.");
        }
        if (is_null($project)) {
            throw new \Exception("Project with id $projectId not found.");
        }

        /** @var EmployeeProject|null $employeeProject */
        $employeeProject = $em->getRepository(EmployeeProject::class)->findOneBy([
            'employee' => $employee,
            'project' => $project
        ]);

        $em->getConnection()->beginTransaction();
        try {
            if (is_null($employeeProject)) {
                $employeeProject = new EmployeeProject();
                $employeeProject
                    ->setEmployee($employee)
                    ->setProject($project)
                    ->setCreatedAt();
                $em->persist($employeeProject);
            }
            $employeeProject
                ->setDateFrom($dateFrom)
                ->setDateTo($dateTo)
                ->setUpdatedAt();
            $em->getConnection()->commit();

            $this->dispatcher->dispatch(new ProjectAssignEvent($employee, $project));

            for ($i = 0; $i < min(count($dateFrom), count($dateTo)); $i++) {
                $from = \DateTime::createFromFormat('d-m-Y', '01-'.$dateFrom[$i])->getTimestamp();
                $toString = date('t-m-Y', strtotime('01-'.$dateTo[$i]));
                $to = \DateTime::createFromFormat('d-m-Y', $toString)->getTimestamp();
                $current = time();
                if ($current >= $from && $current <= $to) {
                    $this->addAccess($employee, $project);
                    break;
                }
            }
        } catch (\Exception $e) {
            $em->getConnection()->rollBack();
        }
    }

    private function addAccess(Employee $employee, Project $project) : void
    {
        $events = AbstractEmployeeProjectAccessEvent::createEvents($employee, $project, true);
        foreach ($events as $event) {
            $this->dispatcher->dispatch($event);
        }
    }
}
