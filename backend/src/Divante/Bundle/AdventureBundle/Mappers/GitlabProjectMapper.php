<?php

namespace Divante\Bundle\AdventureBundle\Mappers;

use Divante\Bundle\AdventureBundle\Entity\GitlabProject;

class GitlabProjectMapper implements Mapper
{

    /**
     * @param GitlabProject $entity
     * @return array<string,mixed>
     */
    public function mapEntity($entity): array
    {
        return [
            'id' => $entity->getId(),
            'type' => $entity->getGitlabType(),
            'name' => $entity->getName()
        ];
    }

    /**
     * @param GitlabProject $entity
     * @return array<string,mixed>
     */
    public function __invoke(GitlabProject $entity) : array
    {
        return $this->mapEntity($entity);
    }
}
