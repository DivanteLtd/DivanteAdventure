<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 * Date: 04.01.19
 * Time: 09:44
 */

namespace Divante\Bundle\AdventureBundle\MessageHandler\Project;

use Divante\Bundle\AdventureBundle\Entity\EmployeeProject;
use Divante\Bundle\AdventureBundle\Entity\Project;
use Divante\Bundle\AdventureBundle\Message\Project\DeleteProject;
use Doctrine\ORM\EntityManagerInterface;

class DeleteProjectHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param DeleteProject $message
     * @throws \Exception
     */
    public function __invoke(DeleteProject $message) : void
    {
        $em = $this->em;
        $id = $message->getEntryId();

        $employeeProjectRepo = $em->getRepository(EmployeeProject::class);
        $pairs = $employeeProjectRepo->findBy(['project' => $id]);
        if (!empty($pairs)) {
            foreach ($pairs as $pair) {
                if (count($pair->getDateFrom()) > 0) {
                    throw new \Exception("Can not delete project - at least one employee is still connected");
                }
            }
        }

        $entry = $em->getRepository(Project::class)->find($id);
        if (is_null($entry)) {
            throw new \Exception("Project entry with id $id not found.");
        }
        $em->remove($entry);
        $em->flush();
    }
}
