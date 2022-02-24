<?php

namespace Divante\Bundle\AdventureBundle\Command;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Repository\EmployeeRepository;
use Divante\Bundle\AdventureBundle\Entity\Repository\TribeRotationHistoryRepository;
use Divante\Bundle\AdventureBundle\Entity\TribeRotationHistory;
use Divante\Bundle\AdventureBundle\Message\Employee\PersonTribeStats;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class FillStatisticTribeCommand extends ContainerAwareCommand
{
    protected EntityManagerInterface $em;
    protected MessageBusInterface $messageBus;
    private const HIRED_AT = 'hired at';
    private const HIRED_TO = 'hired to';

    public function __construct(EntityManagerInterface $em, MessageBusInterface $messageBus)
    {
        $this->em = $em;
        $this->messageBus = $messageBus;
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure() : void
    {
        $this
            ->setName('adventure:fill:tribe:rotation:stats')
            ->setDescription('Fill tribe/departments rotate stats.');
    }

    /**
     * @inheritdoc
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        $output->write("Delete all records from  tribe_rotation_history. ");
        /** @var TribeRotationHistoryRepository $repo */
        $repo = $this->em->getRepository(TribeRotationHistory::class);
        $repo->deleteAll();
        /** @var EmployeeRepository $employeeRepo */
        $employeeRepo = $this->em->getRepository(Employee::class);
        $employees = $employeeRepo->getAll();
        foreach ($employees as $employee) {
            $employ = new PersonTribeStats(
                $employee->getTribe()->getName(),
                $employee->getId(),
                $employee->getHiredAt(),
                self::HIRED_AT
            );
            $this->messageBus->dispatch($employ);
            $output->writeln(
                sprintf(
                    '%s %s join %s',
                    $employee->getName(),
                    $employee->getLastName(),
                    $employee->getTribe()->getName()
                )
            );
            if (!is_null($employee->getHiredTo())) {
                $fire = new PersonTribeStats(
                    $employee->getTribe()->getName(),
                    $employee->getId(),
                    $employee->getHiredAt(),
                    self::HIRED_TO
                );
                $this->messageBus->dispatch($fire);
                $output->writeln(
                    sprintf(
                        '%s %s leave %s',
                        $employee->getName(),
                        $employee->getLastName(),
                        $employee->getTribe()->getName()
                    )
                );
            }
        }
        $this->em->flush();
        return 0;
    }
}
