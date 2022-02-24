<?php

namespace Divante\Bundle\AdventureBundle\Command\Integration;

use Divante\Bundle\AdventureBundle\Entity\EmployeeProject;
use Divante\Bundle\AdventureBundle\Entity\GitlabProject;
use Divante\Bundle\AdventureBundle\Events\Integration\AbstractEmployeeProjectAccessEvent;
use Divante\Bundle\AdventureBundle\Events\Integration\AddGitlabAccessEvent;
use Divante\Bundle\AdventureBundle\Events\Integration\RemoveGitlabAccessEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CheckIntegrationTimeCommand extends ContainerAwareCommand
{
    private EntityManagerInterface $em;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher)
    {
        parent::__construct(null);
        $this->em = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    protected function configure() : void
    {
        $this
            ->setName('adventure:integration:period')
            ->setDescription("Update access based on periods in projects. Should be called once every month.");
    }

    public function run(InputInterface $input, OutputInterface $output) : int
    {
        $employeeProjects = $this->getAllEmployeeProjects();
        $startingPairings = array_filter($employeeProjects, [ $this, 'filterStartingProject' ]);
        $endingPairings = array_filter($employeeProjects, [ $this, 'filterEndingProject' ]);
        $pairingsToIgnore = array_intersect($startingPairings, $endingPairings);
        /** @var EmployeeProject[] $pairingsToCreate */
        $pairingsToCreate = array_diff($startingPairings, $pairingsToIgnore);
        /** @var EmployeeProject[] $pairingsToDelete */
        $pairingsToDelete = array_diff($endingPairings, $pairingsToIgnore);

        foreach ($pairingsToCreate as $pairing) {
            $events = $this->buildEvents($pairing, true);
            foreach ($events as $event) {
                $this->eventDispatcher->dispatch($event);
            }
        }
        foreach ($pairingsToDelete as $pairing) {
            $events = $this->buildEvents($pairing, false);
            foreach ($events as $event) {
                $this->eventDispatcher->dispatch($event);
            }
        }
        return 0;
    }

    /**
     * @param EmployeeProject $pairing
     * @return AbstractEmployeeProjectAccessEvent[]
     */
    private function buildEvents(EmployeeProject $pairing, bool $addAccess) : array
    {
        $events = [];
        $project = $pairing->getProject();
        $employee = $pairing->getEmployee();
        /** @var GitlabProject $gitlabProject */
        foreach ($project->getGitlabProjects()->toArray() as $gitlabProject) {
            if ($addAccess) {
                $events[] = new AddGitlabAccessEvent($employee, $project, $gitlabProject);
            } else {
                $events[] = new RemoveGitlabAccessEvent($employee, $project, $gitlabProject);
            }
        }
        return $events;
    }

    private function filterEndingProject(EmployeeProject $employeeProject) : bool
    {
        $currentDate = new \DateTime();
        $monthAgo = $currentDate->sub(\DateInterval::createFromDateString('1 month'));
        $formattedMonth = $monthAgo->format('m-Y');
        return in_array($formattedMonth, $employeeProject->getDateTo());
    }

    private function filterStartingProject(EmployeeProject $employeeProject) : bool
    {
        $currentDate = new \DateTime();
        $currentMonth = $currentDate->format('m-Y');
        return in_array($currentMonth, $employeeProject->getDateFrom());
    }

    /** @return EmployeeProject[] */
    private function getAllEmployeeProjects() : array
    {
        $repo = $this->em->getRepository(EmployeeProject::class);
        /** @var EmployeeProject[] $result */
        $result = $repo->findAll();
        return $result;
    }
}
