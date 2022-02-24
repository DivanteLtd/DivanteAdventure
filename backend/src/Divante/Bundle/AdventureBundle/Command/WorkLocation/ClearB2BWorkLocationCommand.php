<?php

namespace Divante\Bundle\AdventureBundle\Command\WorkLocation;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\EmployeeWorkLocation;
use Divante\Bundle\AdventureBundle\Entity\Repository\WorkLocation\WorkLocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearB2BWorkLocationCommand extends Command
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct();
    }

    protected function configure() : void
    {
        $this
            ->setName('adventure:clear:work-location')
            ->setDescription('Clear B2B employees work location data if older than 45 days');
    }

    public function run(InputInterface $input, OutputInterface $output) : int
    {
        /** @var WorkLocationRepository $repoWorkLocationRepo */
        $repoWorkLocationRepo = $this->em->getRepository(EmployeeWorkLocation::class);
        $workLocation = $repoWorkLocationRepo->queryOldDates();
        foreach ($workLocation as $entry) {
            $employeeRepo = $this->em->getRepository(Employee::class);
            $employeeId = $entry->getEmployeeId();
            /** @var Employee $employee */
            $employee = $employeeRepo->find($employeeId);
            if (!empty($employee) && ($employee->getContractId() === Employee::CONTRACT_B2B_HOURLY
                || $employee->getContractId() === Employee::CONTRACT_B2B_LUMP_SUM)) {
                $this->em->remove($entry);
            }
        }
        $this->em->flush();
        return 0;
    }
}
