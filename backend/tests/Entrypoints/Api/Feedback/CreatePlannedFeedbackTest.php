<?php

namespace Tests\Entrypoints\Api\Feedback;

use DateTime;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class CreatePlannedFeedbackTest extends AbstractFeedbackTest
{

    private ?User $leaderUser = null;
    private ?User $padawanUser = null;
    private ?Employee $leader = null;
    private ?Employee $padawan = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->leaderUser = $this->generateFosUser(['ROLE_USER']);
        $this->padawanUser = $this->generateFosUser(['ROLE_USER']);
        $this->leader = $this->generateEmployee($this->leaderUser);
        $this->padawan = $this->generateEmployee($this->padawanUser);
        $this->padawan->getLeaders()->add($this->leader);
        $this->em->flush();
    }

    protected function tearDown(): void
    {
        $this->em->remove($this->leader);
        $this->em->remove($this->leaderUser);
        $this->em->remove($this->padawan);
        $this->em->remove($this->padawanUser);
        $this->em->flush();
        parent::tearDown();
    }

    public function testEntrypoint(): void
    {
        $date = (new DateTime())->setDate(rand(2000, 2100), rand(1, 12), rand(1, 25))->format('Y-m-d');
        $request = [
            'date' => $date,
            'employeeId' => $this->padawan->getId(),
        ];
        $response = $this->request('POST', '/api/feedback/planned', $this->leaderUser, $request);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }
}
