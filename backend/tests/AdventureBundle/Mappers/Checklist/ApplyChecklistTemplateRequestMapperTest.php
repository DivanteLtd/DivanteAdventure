<?php

namespace Tests\AdventureBundle\Mappers\Checklist;

use Divante\Bundle\AdventureBundle\Mappers\Checklist\ApplyChecklistTemplateRequestMapper;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\AdventureTestUtils;
use Tests\FoundationTestCase;

class ApplyChecklistTemplateRequestMapperTest extends FoundationTestCase
{
    public function testTemplateIdPassed() : void
    {
        $templateId = rand(0, 10000);
        $request = AdventureTestUtils::createRequest([
            'ownerId' => [rand(0, 10000)],
            'subjectId' => rand(0, 10000),
            'dueDate' => '2020-11-15',
        ]);
        $message = $this->mapper()->mapToMessage($request, $templateId);
        $this->assertEquals($templateId, $message->getTemplateId());
    }

    public function testOwnerIdValuePassed() : void
    {
        $ownerId = [rand(0, 10000)];
        $request = AdventureTestUtils::createRequest([
            'ownerId' => $ownerId,
            'subjectId' => rand(0, 10000),
            'dueDate' => '2020-11-15',
        ]);
        $message = $this->mapper()->mapToMessage($request, rand(0, 10000));
        $this->assertEquals($ownerId, $message->getOwnerIds());
    }

    public function testOwnerIdNullPassed() : void
    {
        $request = AdventureTestUtils::createRequest([
            'subjectId' => rand(0, 10000),
            'dueDate' => '2020-11-15',
        ]);
        $message = $this->mapper()->mapToMessage($request, rand(0, 10000));
        $this->assertEmpty($message->getOwnerIds());
    }

    public function testDueDatePassed() : void
    {
        $dueDate = '2020-11-15';
        $request = AdventureTestUtils::createRequest([
            'ownerId' => [rand(0, 10000)],
            'subjectId' => rand(0, 10000),
            'dueDate' => $dueDate,
        ]);
        $message = $this->mapper()->mapToMessage($request, rand(0, 10000));
        $this->assertEquals($dueDate, $message->getDueDate());
    }

    public function testSubjectIdPassed() : void
    {
        $subjectId = rand(0, 10000);
        $request = AdventureTestUtils::createRequest([
            'ownerId' => [rand(0, 10000)],
            'subjectId' => $subjectId,
            'dueDate' => '2020-11-15',
        ]);
        $message = $this->mapper()->mapToMessage($request, rand(0, 10000));
        $this->assertEquals($subjectId, $message->getSubjectId());
    }

    public function testExceptionOnLackingSubjectId() : void
    {
        $request = AdventureTestUtils::createRequest([
            'ownerId' => [rand(0, 10000)],
            'dueDate' => '2020-11-15',
        ]);
        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessageMatches('/.*subjectId.*required.*/i');
        $this->mapper()->mapToMessage($request, rand(0, 10000));
    }

    public function testExceptionOnOwnerIdWrongType() : void
    {
        $request = AdventureTestUtils::createRequest([
            'ownerId' => "StringIsNotInt",
            'dueDate' => '2020-11-15',
        ]);
        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessageMatches('/.*ownerId.*array.*/i');
        $this->mapper()->mapToMessage($request, rand(0, 10000));
    }

    public function testExceptionOnSubjectIdWrongType() : void
    {
        $request = AdventureTestUtils::createRequest([
            'ownerId' => [rand(0, 10000)],
            'subjectId' => "StringIsNotInt",
            'dueDate' => '2020-11-15',
        ]);
        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessageMatches('/.*subjectId.*int.*/i');
        $this->mapper()->mapToMessage($request, rand(0, 10000));
    }

    private function mapper() : ApplyChecklistTemplateRequestMapper
    {
        return new ApplyChecklistTemplateRequestMapper();
    }
}
