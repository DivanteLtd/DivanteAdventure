<?php
/**
 * Created by PhpStorm.
 * User: nk
 * Date: 28.01.19
 * Time: 13:32
 */

namespace Divante\Bundle\AdventureBundle\Command;

use Divante\Bundle\AdventureBundle\Entity\Agreement;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Message\Agreement\CreateEmployeeAgreement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class CheckAgreement extends Command
{
    private EntityManagerInterface $em;
    protected MessageBusInterface $messageBus;

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
            ->setName('adventure:check:agreement')
            ->setDescription('Check all agreement for all users.');
    }

    /**
     * @inheritdoc
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        $output->write("Getting users..");
        $userRepo = $this->em->getRepository(Employee::class);
        $users = $userRepo->findAll();
        $output->write("Getting agreements..");
        $agreementRepo = $this->em->getRepository(Agreement::class);
        $agreements = $agreementRepo->findAll();
        foreach ($users as $user) {
            $output->write("Check agreements for " . $user->getEmail());
            foreach ($agreements as $agreement) {
                $message = new CreateEmployeeAgreement($user->getId(), $agreement->getId());
                $this->messageBus->dispatch($message);
            }
        }
        $output->writeln("done.");
        return 0;
    }
}
