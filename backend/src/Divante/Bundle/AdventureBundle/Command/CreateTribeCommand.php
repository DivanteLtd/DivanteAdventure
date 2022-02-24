<?php

namespace Divante\Bundle\AdventureBundle\Command;

use Divante\Bundle\AdventureBundle\Entity\Tribe;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateTribeCommand extends Command
{
    private const ARGUMENT_NAME = 'name';
    private const OPTION_DEPARTMENT = 'department';

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('adventure:tribe:create')
            ->addArgument(self::ARGUMENT_NAME, InputArgument::REQUIRED, 'Tribe name')
            ->addOption(self::OPTION_DEPARTMENT, 'd', InputOption::VALUE_NONE, '')
            ->setDescription("Creates a new tribe");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $name */
        $name = $input->getArgument(self::ARGUMENT_NAME);
        /** @var bool $department */
        $department = $input->getOption(self::OPTION_DEPARTMENT);

        if ($department) {
            $output->write(sprintf("Creating department %s...", $name));
        } else {
            $output->write(sprintf("Creating tribe %s...", $name));
        }
        $tribe = new Tribe();
        $tribe->setName($name)
            ->setVirtual($department)
            ->setCreatedAt()
            ->setUpdatedAt();

        $this->em->persist($tribe);
        $this->em->flush();

        $output->writeln(' done.');
        return 0;
    }
}
