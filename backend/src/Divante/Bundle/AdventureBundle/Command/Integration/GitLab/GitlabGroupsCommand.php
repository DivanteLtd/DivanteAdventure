<?php

namespace Divante\Bundle\AdventureBundle\Command\Integration\GitLab;

use Divante\Bundle\AdventureBundle\Entity\GitlabProject;

class GitlabGroupsCommand extends AbstractGitlabDownloadCommand
{
    protected function getCommandName(): string
    {
        return 'groups';
    }

    protected function getCommandDescription(): string
    {
        return 'Synchronizes GitLab groups with Adventure';
    }

    protected function getApiUrl(): string
    {
        return '{GITLAB_URL}api/v4/groups?per_page=100&page={PAGE}';
    }

    protected function getGitlabType() : int
    {
        return GitlabProject::GITLAB_TYPE_GROUP;
    }

    /**
     * @param array<string,mixed> $data
     * @return string
     */
    protected function getIdFromResponse(array $data): string
    {
        return $data['id'];
    }

    /**
     * @param array<string,mixed> $data
     * @return string
     */
    protected function getNameFromResponse(array $data): string
    {
        return $data['full_name'];
    }
}
