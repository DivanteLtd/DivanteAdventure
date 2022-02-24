<?php

namespace Tests\Entrypoints\Api\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Checklist\Checklist;

class UnitedChecklistVisibleForSubjectTest extends AbstractChecklistTest
{
    public function testVisibleForOwner(): void
    {
        $randomName = "RandomName".rand(0, 10000);
        $checklist = $this->buildChecklist(
            $randomName,
            Checklist::TYPE_UNITED,
            true,
            $this->employee,
            $this->backupEmployee,
        );

        $response = $this->request("GET", '/api/checklist/mine', $this->user);
        $this->assertTrue($this->checklistVisible($response, $randomName));

        $this->em->remove($this->em->merge($checklist));
        $this->em->flush();
    }

    public function testVisibleForSubject(): void
    {
        $randomName = "RandomName".rand(0, 10000);
        $checklist = $this->buildChecklist(
            $randomName,
            Checklist::TYPE_UNITED,
            true,
            $this->backupEmployee,
            $this->employee,
        );

        $response = $this->request("GET", '/api/checklist/mine', $this->user);
        $this->assertTrue($this->checklistVisible($response, $randomName));

        $this->em->remove($this->em->merge($checklist));
        $this->em->flush();
    }
}
