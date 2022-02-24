<?php

namespace Divante\Bundle\AdventureBundle\Command\Integration\GitLab;

use Divante\Bundle\AdventureBundle\Entity\GitlabProject;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractGitlabDownloadCommand extends AbstractGitlabCommand
{
    protected function callApi(OutputInterface $output): void
    {
        $page = 0;
        /** @var EntityManagerInterface $em */
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        do {
            $page++;
            $output->writeln("Downloading page $page...");
            $currentPage = $this->runRequest(
                $this->getApiUrl(),
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
    private function readPage(array $page, OutputInterface $output, EntityManagerInterface $em) : void
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
        $id = (int)$this->getIdFromResponse($entry);
        $name = $this->getNameFromResponse($entry);
        $output->write("\tChecking entry #$id '$name'...");
        $repo = $em->getRepository(GitlabProject::class);

        /** @var GitlabProject|null $gitlabProject */
        $gitlabProject = $repo->findOneBy([
            'gitlabId' => $id,
            'gitlabType' => $this->getGitlabType()
        ]);
        if (is_null($gitlabProject)) {
            $gitlabProject = (new GitlabProject())
                ->setGitlabId($id)
                ->setGitlabType($this->getGitlabType())
                ->setName($name)
                ->setCreatedAt()
                ->setUpdatedAt();
            $em->persist($gitlabProject);
            $output->writeln("Created new entry in database.");
        } elseif ($gitlabProject->getName() !== $name) {
            $gitlabProject->setName($name);
            $output->writeln("Updated name of entry with given ID");
        } else {
            $output->writeln("Entry is already up-to-date");
        }
    }

    abstract protected function getApiUrl() : string;
    abstract protected function getGitlabType() : int;

    /**
     * @param array<string,mixed> $data
     * @return string
     */
    abstract protected function getIdFromResponse(array $data) : string;

    /**
     * @param array<string,mixed> $data
     * @return string
     */
    abstract protected function getNameFromResponse(array $data) : string;
}
