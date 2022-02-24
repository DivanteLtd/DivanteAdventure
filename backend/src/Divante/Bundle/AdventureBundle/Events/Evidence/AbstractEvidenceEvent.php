<?php

namespace Divante\Bundle\AdventureBundle\Events\Evidence;

use Divante\Bundle\AdventureBundle\Entity\Evidence;
use Symfony\Contracts\EventDispatcher\Event;

class AbstractEvidenceEvent extends Event
{
    private Evidence $evidence;

    public function __construct(Evidence $evidence)
    {
        $this->evidence = $evidence;
    }

    public function getEvidence() : Evidence
    {
        return $this->evidence;
    }
}
