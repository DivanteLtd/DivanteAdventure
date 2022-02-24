<?php

namespace Divante\Bundle\AdventureBundle\Command\Integration\GitLab;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\GitlabProject;
use Divante\Bundle\AdventureBundle\Entity\IntegrationQueue;
use Divante\Bundle\AdventureBundle\Services\GitLab\AddAccess;
use Divante\Bundle\AdventureBundle\Services\GitLab\RemoveAccess;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Output\OutputInterface;

class GitlabUpdatesCommand extends AbstractGitlabCommand
{
    private RemoveAccess $removeAccess;
    private AddAccess $addAccess;
    private EntityManagerInterface $em;

    public function __construct(RemoveAccess $removeAccess, AddAccess $addAccess, EntityManagerInterface $em)
    {
        parent::__construct();
        $this->removeAccess = $removeAccess;
        $this->addAccess = $addAccess;
        $this->em = $em;
    }

    protected function getCommandName(): string
    {
        return 'update';
    }

    protected function getCommandDescription(): string
    {
        return 'Send update requests to GitLab';
    }

    protected function callApi(OutputInterface $output): void
    {
        /** @var IntegrationQueue $request */
        foreach ($this->getQueue() as $request) {
            $this->handleRequest($request, $output);
        }
        $output->write("Flushing... ");
        $this->em->flush();
        $output->writeln("finished.");
    }

    /**
     * @return IntegrationQueue[]
     */
    private function getQueue() : array
    {
        $queue = $this->em->getRepository(IntegrationQueue::class);
        /** @var IntegrationQueue[] $results */
        $results = $queue->findBy([
            'type' => [
                IntegrationQueue::TYPE_GITLAB_ADD,
                IntegrationQueue::TYPE_GITLAB_REMOVE,
            ],
            'status' => IntegrationQueue::STATUS_ENQUEUED,
        ], [ 'createdAt' => 'asc'], 100);
        return $results;
    }

    private function handleRequest(IntegrationQueue $request, OutputInterface $output) : void
    {
        $employee = $request->getEmployee();
        $gitlabProject = $this->getGitlabProject($request);

        if (is_null($gitlabProject)) {
            $request->setStatus(IntegrationQueue::STATUS_INVALID);
            $output->writeln(sprintf(
                "Request #%d had type %s, but no gitlab ID passed",
                $request->getId(),
                $request->getType()
            ));
        }

        try {
            if ($request->getType() === IntegrationQueue::TYPE_GITLAB_ADD) {
                $this->addAccess($employee, $gitlabProject, $output);
            } elseif ($request->getType() === IntegrationQueue::TYPE_GITLAB_REMOVE) {
                $this->removeAccess($employee, $gitlabProject, $output);
            }
            $request->setStatus(IntegrationQueue::STATUS_DONE);
        } catch (Exception $e) {
            $output->writeln("Error: ".$e->getMessage());
            $request->setStatus(IntegrationQueue::STATUS_INVALID);
        }
    }

    private function getGitlabProject(IntegrationQueue $request) : ?GitlabProject
    {
        $projectId = $request->getRequestData()['gitlab_project_id'] ?? -1;
        if ($projectId === -1) {
            return null;
        }
        $repo = $this->em->getRepository(GitlabProject::class);
        /** @var GitlabProject|null $found */
        $found = $repo->find($projectId);
        return $found;
    }

    /**
     * @param Employee $employee
     * @param GitlabProject $gitlabProject
     * @param OutputInterface $output
     * @throws Exception
     */
    private function addAccess(Employee $employee, GitlabProject $gitlabProject, OutputInterface $output) : void
    {
        $output->write(sprintf(
            "Adding access to Gitlab %s '%s' (#%d) for employee %s...",
            $gitlabProject->getGitlabType() === GitlabProject::GITLAB_TYPE_REPOSITORY ? 'project' : 'group',
            $gitlabProject->getName(),
            $gitlabProject->getGitlabId(),
            $employee->getName().' '.$employee->getLastName()
        ));
        $this->addAccess->addAccess($employee, $gitlabProject);
        $output->writeln("done.");
    }

    /**
     * @param Employee $employee
     * @param GitlabProject $gitlabProject
     * @param OutputInterface $output
     * @throws Exception
     */
    private function removeAccess(Employee $employee, GitlabProject $gitlabProject, OutputInterface $output) : void
    {
        $output->write(sprintf(
            "Removing access to Gitlab %s '%s' (#%d) from employee %s...",
            $gitlabProject->getGitlabType() === GitlabProject::GITLAB_TYPE_REPOSITORY ? 'project' : 'group',
            $gitlabProject->getName(),
            $gitlabProject->getGitlabId(),
            $employee->getName().' '.$employee->getLastName()
        ));
        $this->removeAccess->removeAccess($employee, $gitlabProject);
        $output->writeln("done.");
    }
}
