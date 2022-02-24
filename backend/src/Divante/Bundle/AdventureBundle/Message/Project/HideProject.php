<?php
/**
 * Created by PhpStorm.
 * User: ŁB
 * Date: 13.02.19
 * Time: 11:55
 */

namespace Divante\Bundle\AdventureBundle\Message\Project;

use Divante\Bundle\AdventureBundle\Entity\Project;
use Divante\Bundle\AdventureBundle\Infrastructure\Messenger\ObjectTrait;

class HideProject
{
    use ObjectTrait;

    private Project $entry;
    private bool $archived;

    public function __construct(Project $entry, bool $visibility)
    {
        $this->entry    = $entry;
        $this->archived = $visibility;
    }

    public function getEntry(): Project
    {
        return $this->entry;
    }

    public function isArchived(): bool
    {
        return $this->archived;
    }
}
