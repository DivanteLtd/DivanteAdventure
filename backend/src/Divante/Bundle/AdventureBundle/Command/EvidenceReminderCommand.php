<?php


namespace Divante\Bundle\AdventureBundle\Command;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Repository\EmployeeRepository;
use Divante\Bundle\AdventureBundle\Representation\Email\EmailConfiguration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Translation\TranslatorInterface;

class EvidenceReminderCommand extends Command
{
    private EntityManagerInterface $em;
    private EmailConfiguration $email;
    private TranslatorInterface $translator;

    public function __construct(
        EntityManagerInterface $em,
        EmailConfiguration $emailConfiguration,
        TranslatorInterface $translator
    ) {
        $this->em = $em;
        $this->email = $emailConfiguration;
        $this->translator= $translator;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('adventure:evidence:reminder')
            ->setDescription("Reminds you to send the evidence");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($this->sendReminder() === false) {
            return 0;
        }
        /** @var EmployeeRepository $employeeRepo */
        $employeeRepo = $this->em->getRepository(Employee::class);
        $employees = $employeeRepo->getEmployeeByCivilLawContract();
        $date = (new \DateTime())->format('m.Y');
        foreach ($employees as $employee) {
            $lang = $employee->getLanguage();
            $this->translator->setLocale($lang);
            $email = $employee->getEmail();
            $subject = $this->translator->trans('evidence.reminder.subject');
            $this->email->sendMessage(
                $email,
                null,
                $subject,
                ['date' => $date],
                'AdventureBundle:Emails:evidence_reminder.html.twig'
            );
        }
        return 0;
    }

    private function sendReminder() :bool
    {
        $today = new \DateTime();
        $daysInMonth = (int)$today->format('t');
        $numberOfDay = (int)$today->format('j');
        if ($daysInMonth - $numberOfDay == 1) {
            return true;
        }
        return false;
    }
}
