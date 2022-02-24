<?php

namespace Tests\Entrypoints\Api\Checklist;

use DateTime;
use Divante\Bundle\AdventureBundle\Entity\Checklist\Checklist;
use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistQuestion;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\User;
use Divante\Bundle\AdventureBundle\Annotation\Exporter;
use Symfony\Component\HttpFoundation\Response;
use Tests\Entrypoints\AbstractEntrypointTest;

abstract class AbstractChecklistTest extends AbstractEntrypointTest
{
    protected ?User $user = null;
    protected ?User $backupUser = null;
    protected ?Employee $employee = null;
    protected ?Employee $backupEmployee = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->generateFosUser();
        $this->backupUser = $this->generateFosUser();
        $this->employee = $this->generateEmployee($this->user);
        $this->backupEmployee = $this->generateEmployee($this->backupUser);
        $this->em->flush();
    }

    protected function tearDown(): void
    {
        $this->em->remove($this->em->merge($this->employee));
        $this->em->remove($this->em->merge($this->user));
        $this->em->remove($this->em->merge($this->backupEmployee));
        $this->em->remove($this->em->merge($this->backupUser));
        $this->em->flush();
        parent::tearDown();
    }

    protected function buildChecklist(
        string $name,
        int $type,
        bool $visible,
        Employee $owner,
        Employee $subject,
        ?Employee $taskResponsible = null
    ): Checklist {
        $checklist = (new Checklist())
            ->setCreatedAt()
            ->setUpdatedAt()
            ->setStartedAt(new DateTime())
            ->setDueDate(new DateTime())
            ->setHidden(!$visible)
            ->setType($type)
            ->setNameEn($name)
            ->setNamePl('')
            ->setSubject($subject)
            ->setOwners([ $owner ]);
        if (!is_null($taskResponsible)) {
            $question = new ChecklistQuestion();
            $question->setNamePl('');
            $question->setNameEn('');
            $question->setDescriptionEn('');
            $question->setDescriptionPl('');
            $question->setUpdatedAt();
            $question->setCreatedAt();
            $question->setCurrentStatus(0);
            $question->setPossibleStatuses([[]]);
            $question->setChecklist($checklist);
            $question->setResponsible($taskResponsible);
            $checklist->addQuestion($question);
            $this->em->persist($question);
        }
        $this->em->persist($checklist);
        $this->em->flush();
        return $checklist;
    }

    protected function checklistVisible(Response $response, string $name): bool
    {
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        /** @var array<string,array<string|int,mixed>> $json */
        $json = json_decode($response->getContent(), true);
        return !empty(array_filter(
            $json,
            fn(array $entry) => ($entry['nameEn'] ?? '') === $name,
        ));
    }
}
