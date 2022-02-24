<?php

namespace Divante\Bundle\AdventureBundle\Entity\Repository;

use Divante\Bundle\AdventureBundle\Entity\EmployeeProject;
use Divante\Bundle\AdventureBundle\Entity\Project;
use Doctrine\ORM\EntityRepository;

class EmployeeProjectRepository extends EntityRepository
{
    /**
     * @param Project $project
     * @return mixed
     */
    public function getAllEmployeeWorkInProject(Project $project)
    {
        $employees = $this->findBy([
            'project' => $project->getId()
        ]);

        $previously = [];
        $currently = [];
        /** @var EmployeeProject $employee */
        foreach ($employees as $employee) {
            $datesTo = $employee->getDateTo();
            $flag = true;
            if (count($datesTo) > 0) {
                foreach ($datesTo as $dateTo) {
                    if (\DateTime::createFromFormat('m-Y', $dateTo)->getTimestamp()
                        >= \DateTime::createFromFormat('m-Y', date('m-Y'))->getTimestamp()) {
                        array_push(
                            $currently,
                            [
                                'lastName' => $employee->getEmployee()->getLastName(),
                                'name' => $employee->getEmployee()->getName(),
                            ]
                        );
                        $flag = false;
                        break;
                    }
                }
                if ($flag) {
                    array_push(
                        $previously,
                        [
                            'lastName' => $employee->getEmployee()->getLastName(),
                            'name' => $employee->getEmployee()->getName(),
                        ]
                    );
                }
            }
        }
        return $result =
            [
                'currently' => $currently,
                'previously' => $previously
            ];
    }
}
