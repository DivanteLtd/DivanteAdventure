<?php

namespace Divante\Bundle\AdventureBundle\Command\Integration\GitLab;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GitlabUsersCommand extends AbstractGitlabCommand
{
    private const API_URL = '{GITLAB_URL}api/v4/users?per_page=100&page={PAGE}';

    protected function getCommandName(): string
    {
        return 'users';
    }

    protected function getCommandDescription(): string
    {
        return 'Synchronizes Gitlab users with Adventure';
    }

    protected function callApi(OutputInterface $output): void
    {
        /** @var EntityManagerInterface $em */
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $page = 0;
        do {
            $page++;
            $output->writeln("Downloading page $page...");
            $currentPage = $this->runRequest(
                self::API_URL,
                'GET',
                [ 'GITLAB_URL' => $this->getInstanceUrl(), 'PAGE' => $page ]
            );
            if (count($currentPage) > 0) {
                $this->readPage($currentPage, $output, $em);
                $output->writeln("Page $page finished.");
            } else {
                $output->writeln("Page $page is empty.");
            }
        } while (count($currentPage) > 0);
        $output->write("Flushing... ");
        $em->flush();
        $output->writeln("completed.");
    }

    /**
     * @param array<int,array<string,mixed>> $page
     * @param OutputInterface $output
     * @param EntityManagerInterface $em
     */
    private function readPage(array $page, OutputInterface $output, EntityManagerInterface $em): void
    {
        /** @var array<string,mixed> $entry */
        foreach ($page as $entry) {
            $this->readEntry($entry, $output, $em);
        }
    }

    /**
     * @param array<string,mixed> $entry
     * @param OutputInterface $output
     * @param EntityManagerInterface $em
     */
    private function readEntry(array $entry, OutputInterface $output, EntityManagerInterface $em) : void
    {
        /** @var EmployeeRepository $repo */
        $repo = $em->getRepository(Employee::class);
        $email = $entry['email'];
        $gitlabId = $entry['id'];
        $output->write("Checking email $email... ");
        $employee = $repo->findOneByEmailAddressUsername($email);
        if (is_null($employee)) {
            $output->writeln("Employee not found.");
        } elseif ($employee->getGitlabId() === $gitlabId) {
            $output->writeln("Employee found, Gitlab ID is up-to-date.");
        } else {
            $employee->setGitlabId($gitlabId);
            $output->writeln("Employee found, updated GitlabID ID to #$gitlabId");
        }
    }
}
