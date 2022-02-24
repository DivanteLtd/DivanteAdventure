<?php

namespace Divante\Bundle\AdventureBundle\Command\WorkLocation;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\EmployeeWorkLocation;
use Divante\Bundle\AdventureBundle\Entity\Repository\WorkLocation\WorkLocationRepository;
use Divante\Bundle\AdventureBundle\Representation\Email\EmailConfiguration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Translation\TranslatorInterface;

class WorkLocationReportCommand extends Command
{
    private EmailConfiguration $email;
    private EntityManagerInterface $em;
    protected TranslatorInterface $translator;

    public function __construct(EmailConfiguration $email, EntityManagerInterface $em, TranslatorInterface $translator)
    {
        $this->email = $email;
        $this->em = $em;
        $this->translator = $translator;
        parent::__construct();
    }

    protected function configure() : void
    {
        $this
            ->setName('adventure:report:work-location')
            ->setDescription('Send report of persons with CLC and CoE contract with their work location 
            of previous month');
    }

    public function run(InputInterface $input, OutputInterface $output) : int
    {
        /** @var WorkLocationRepository $repoWorkLocationRepo */
        $repoWorkLocationRepo = $this->em->getRepository(EmployeeWorkLocation::class);
        $workLocation = $repoWorkLocationRepo->queryLastMonthDates();
        $filteredEntriesRemote = [];
        $filteredEntriesTrip = [];
        foreach ($workLocation as $entry) {
            $employeeRepo = $this->em->getRepository(Employee::class);
            $employeeId = $entry->getEmployeeId();
            /** @var Employee $employee */
            $employee = $employeeRepo->find($employeeId);
            if (!empty($employee) && ($employee->getContractId() === Employee::CONTRACT_B2B_HOURLY
                    || $employee->getContractId() === Employee::CONTRACT_B2B_LUMP_SUM)) {
                $newEntry = [
                    'employeeName' => $employee->getName().' '.$employee->getLastName(),
                    'contract' => $employee->getContractType() === 'CoE' ? 'UoP' : "UCP",
                    'date' => $entry->getDate(),
                    'type' => $entry->getType()
                ];
                if ($entry->getType() === EmployeeWorkLocation::TYPE_REMOTE) {
                    $filteredEntriesRemote[] = $newEntry;
                } else {
                    $filteredEntriesTrip[] = $newEntry;
                }
            }
        }
        $this->translator->setLocale('en');
        $this->email->sendMessage(
            $this->email->getMailerBokAddress(),
            null,
            sprintf('[Adventure] %s', $this->translator->trans('workLocation.reportRemote')),
            ['entries' => $filteredEntriesRemote, 'type' => EmployeeWorkLocation::TYPE_REMOTE],
            'AdventureBundle:Emails:workLocation/work_location_report.html.twig'
        );
        $this->email->sendMessage(
            $this->email->getMailerBokAddress(),
            null,
            sprintf('[Adventure] %s', $this->translator->trans('workLocation.reportTrip')),
            ['entries' => $filteredEntriesTrip, 'type' => EmployeeWorkLocation::TYPE_DELEGATION],
            'AdventureBundle:Emails:workLocation/work_location_report.html.twig'
        );
        return 0;
    }
}
