<?php

namespace Tests\Entrypoints\Api\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Checklist\Checklist;

class UnitedChecklistInvisibleForSubjectTest extends AbstractChecklistTest
{
    public function testVisibleForOwner(): void
    {
        $randomName = "RandomName".rand(0, 10000);
        $checklist = $this->buildChecklist(
            $randomName,
            Checklist::TYPE_UNITED,
            false,
            $this->employee,
            $this->backupEmployee,
        );

        $response = $this->request("GET", '/api/checklist/mine', $this->user);
        $this->assertTrue($this->checklistVisible($response, $randomName));

        $this->em->remove($this->em->merge($checklist));
        $this->em->flush();
    }

    public function testInvisibleForSubject(): void
    {
        $randomName = "RandomName".rand(0, 10000);
        $checklist = $this->buildChecklist(
            $randomName,
            Checklist::TYPE_UNITED,
            false,
            $this->backupEmployee,
            $this->employee,
        );

        $response = $this->request("GET", '/api/checklist/mine', $this->user);
        $this->assertFalse($this->checklistVisible($response, $randomName));

        $this->em->remove($this->em->merge($checklist));
        $this->em->flush();
    }
}
