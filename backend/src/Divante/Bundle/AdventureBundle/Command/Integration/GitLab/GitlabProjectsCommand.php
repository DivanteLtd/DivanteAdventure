<?php

namespace Divante\Bundle\AdventureBundle\Command\Integration\GitLab;

use Divante\Bundle\AdventureBundle\Entity\GitlabProject;

class GitlabProjectsCommand extends AbstractGitlabDownloadCommand
{
    protected function getCommandName(): string
    {
        return 'projects';
    }

    protected function getCommandDescription(): string
    {
        return 'Synchronizes GitLab projects with Adventure';
    }

    protected function getApiUrl(): string
    {
        return '{GITLAB_URL}api/v4/projects?simple=true&per_page=100&page={PAGE}';
    }

    protected function getGitlabType() : int
    {
        return GitlabProject::GITLAB_TYPE_REPOSITORY;
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
        return $data['name_with_namespace'];
    }
}
