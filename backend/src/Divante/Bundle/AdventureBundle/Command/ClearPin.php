<?php


namespace Divante\Bundle\AdventureBundle\Command;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;

class ClearPin extends Command
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure() : void
    {
        $this
            ->setName('adventure:clear:pin')
            ->setDescription('Clears pin.');
    }

    public function run(InputInterface $input, OutputInterface $output)
    {
        $repo = $this->em->getRepository(Employee::class);
        $employees = $repo->findAll();
        $now = new \DateTime();
        /** @var Employee $employee */
        foreach ($employees as $employee) {
            $lastDateResetPin = $employee->getDateResetPin();
            $nextDateResetPin = $lastDateResetPin->add(new \DateInterval("P6M"));

            if ($now >= $nextDateResetPin) {
                $output->writeln($employee->getEmail());
                $employee->setNullAsPin();
                $employee->setDateResetPin($now);
                $this->em->persist($employee);
                $this->em->flush();
            }
        }
        return 0;
    }
}
