<?php

namespace Divante\Bundle\AdventureBundle\Services\GitLab;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\GitlabProject;
use Divante\Bundle\AdventureBundle\Infrastructure\Config\SystemConfig;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AddAccess
{
    private EntityManagerInterface $em;
    private string $gitlabUrl;
    private string $accessToken;

    private const API_URL = '{GITLAB_URL}/api/v4/{TYPE}/{ID}/members';

    public function __construct(SystemConfig $config, EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        $this->gitlabUrl = $config->getValueOrDefault(SystemConfig::KEY_GITLAB_INSTANCE_URL, '');
        $this->accessToken = $config->getValueOrDefault(SystemConfig::KEY_GITLAB_TOKEN, '');
    }

    /**
     * @param Employee $employee
     * @param GitlabProject $gitlabProject
     * @throws \Exception
     */
    public function addAccess(Employee $employee, GitlabProject $gitlabProject) : void
    {
        $uid = $employee->getGitlabId();
        if (is_null($uid)) {
            throw new \Exception("User doesn't have Gitlab ID");
        }
        $url = $this->getRestUrl($gitlabProject);
        $request = [
            'form_params' => [
                'user_id' => $uid,
                'access_level' => 30,
            ],
            'headers' => [ 'Private-Token' => $this->accessToken ]
        ];
        $client = new Client();
        try {
            $client->request('POST', $url, $request);
        } catch (GuzzleException $e) {
            throw new \Exception("GuzzleException: ".$e->getMessage(), 0, $e);
        }
    }

    private function getRestUrl(GitlabProject $project) : string
    {
        return str_replace([
            '{GITLAB_URL}',
            '{TYPE}',
            '{ID}',
        ], [
            $this->gitlabUrl,
            $project->getGitlabType() === GitlabProject::GITLAB_TYPE_REPOSITORY ? 'projects' : 'groups',
            $project->getGitlabId(),
        ], self::API_URL);
    }
}
