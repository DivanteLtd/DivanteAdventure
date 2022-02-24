<?php

namespace Divante\Bundle\AdventureBundle\Controller\Api\Checklist;

use Divante\Bundle\AdventureBundle\Entity\Checklist\Checklist;
use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistQuestion;
use Divante\Bundle\AdventureBundle\Entity\Checklist\ChecklistQuestionInterface;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Repository\Checklist\ChecklistRepository;
use Divante\Bundle\AdventureBundle\Mappers\Checklist\ChecklistDetailsMapper;
use Divante\Bundle\AdventureBundle\Mappers\Checklist\ChecklistListMapper;
use Divante\Bundle\AdventureBundle\Message\Checklist\DeleteChecklist;
use Divante\Bundle\AdventureBundle\Message\Checklist\PingChecklistQuestion;
use Divante\Bundle\AdventureBundle\Message\Checklist\UpdateQuestionStatus;
use Divante\Bundle\AdventureBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Class ChecklistController
 * @package Divante\Bundle\AdventureBundle\Controller\Api\Checklist
 * @Route("api/checklist")
 */
class ChecklistController extends FOSRestController
{
    /**
     * @Route("", name="checklist_list")
     * @Method("GET")
     * @Security("has_role('ROLE_HELPDESK') or has_role('ROLE_HR')")
     * @param EntityManagerInterface $em
     * @param ChecklistListMapper $mapper
     * @return View
     */
    public function getChecklists(EntityManagerInterface $em, ChecklistListMapper $mapper) : View
    {
        $repo = $em->getRepository(Checklist::class);
        /** @var Checklist[] $all */
        $all = $repo->findAll();
        $result = array_map($mapper, $all);
        return $this->view($result, Response::HTTP_OK);
    }

    /**
     * @Route("/{id}", name="checklist_details", requirements={"id"="\d+"})
     * @Method("GET")
     * @param int $id
     * @param EntityManagerInterface $em
     * @param ChecklistDetailsMapper $mapper
     * @return View
     */
    public function getChecklistDetails(int $id, EntityManagerInterface $em, ChecklistDetailsMapper $mapper) : View
    {
        $repo = $em->getRepository(Checklist::class);
        /** @var Checklist|null $checklist */
        $checklist = $repo->find($id);
        if (is_null($checklist)) {
            return $this->view([], Response::HTTP_NOT_FOUND);
        }
        /** @var User $user */
        $user = $this->getUser();
        if (!in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            $employee = $user->getEmployee();
            $myChecklists = $this->getMyUnfinishedChecklists($em, $employee);
            $responsibleChecklists = $this->getMyChecklistsWithResponsibleTasks($em, $employee, $myChecklists);
            $isOwner = !empty($checklist->getOwners()) && $this->isOwner($checklist->getOwners()->toArray(), $user);
            $isSubject = $checklist->getSubject()->getId() === $user->getEmployeeId();
            if (!$user->hasRole('ROLE_HR') && !$user->hasRole('ROLE_HELPDESK')
                && !$isOwner && !$isSubject && !in_array($checklist, $responsibleChecklists)) {
                return $this->view([], Response::HTTP_FORBIDDEN);
            }
        }

        return $this->view($mapper($checklist), Response::HTTP_OK);
    }

    /**
     * @Route("/{checklistId}/{questionId}/ping",
     *      name="checklist_question_ping",
     *      requirements={"checklistId"="\d+", "questionId"="\d+"}
     * )
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     * @param int $questionId
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function pingTask(int $questionId, MessageBusInterface $messageBus) : View
    {
        /** @var User $user */
        $user = $this->getUser();
        $employee = $user->getEmployee();
        $message = new PingChecklistQuestion($questionId, $employee);
        $messageBus->dispatch($message);
        $this->getDoctrine()->getManager()->flush();
        return $this->view([], Response::HTTP_OK);
    }

    /**
     * @Route("/{checklistId}/{questionId}",
     *     name="checklist_question_status_update",
     *     requirements={"checklistId"="\d+", "questionId"="\d+"}
     * )
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     * @param int $checklistId
     * @param int $questionId
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function updateTaskStatus(
        int $checklistId,
        int $questionId,
        Request $request,
        EntityManagerInterface $em,
        MessageBusInterface $messageBus
    ) : View {
        /** @var int|null $status */
        $status = $request->get('status', null);
        if (is_null($status)) {
            throw new BadRequestHttpException("Field 'status' is required");
        } elseif (!is_int($status)) {
            throw new BadRequestHttpException("Field 'status' must be a number");
        }
        /** @var User $user */
        $user = $this->getUser();
        $message = new UpdateQuestionStatus($checklistId, $questionId, $status, $user->getEmployee());
        $messageBus->dispatch($message);
        $em->flush();
        return $this->view([], Response::HTTP_OK);
    }

    /**
     * @Route("/mine", name="checklist_list_mine")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     * @param EntityManagerInterface $em
     * @param ChecklistDetailsMapper $mapper
     * @return View
     */
    public function getMyChecklists(EntityManagerInterface $em, ChecklistDetailsMapper $mapper) : View
    {
        /** @var User $user */
        $user = $this->getUser();
        $employee = $user->getEmployee();
        $myChecklists = $this->getMyUnfinishedChecklists($em, $employee);
        $checklists = [
            ...$myChecklists,
            ...$this->getMyChecklistsWithResponsibleTasks($em, $employee, $myChecklists),
        ];
        $view = array_map($mapper, $checklists);
        return $this->view($view, Response::HTTP_OK);
    }

    /**
     * @param EntityManagerInterface $em
     * @param Employee $employee
     * @return Checklist[]
     */
    private function getMyUnfinishedChecklists(EntityManagerInterface $em, Employee $employee) : array
    {
        /** @var ChecklistRepository $checklistRepo */
        $checklistRepo = $em->getRepository(Checklist::class);
        /** @var Checklist[] $checklists */
        $checklists = $checklistRepo->findByOwnerOrSubject($employee);
        return array_filter(
            $checklists,
            fn(Checklist $checklist) : bool => is_null($checklist->getFinishedAt()) && (!$checklist->isHidden()
                    || $checklist->isHidden() && $checklist->getSubject()->getId() !== $employee->getId())
        );
    }

    /**
     * @param EntityManagerInterface $em
     * @param Employee $employee
     * @param Checklist[] $ownedChecklists
     * @return Checklist[]
     */
    private function getMyChecklistsWithResponsibleTasks(
        EntityManagerInterface $em,
        Employee $employee,
        array $ownedChecklists
    ) : array {
        /** @var int[] $ownedChecklistsIds */
        $ownedChecklistsIds = array_map(fn(Checklist $checklist) => $checklist->getId(), $ownedChecklists);
        $repo = $em->getRepository(ChecklistQuestion::class);
        /** @var ChecklistQuestion[] $tasks */
        $tasks = $repo->findBy([
            'responsible' => $employee,
        ]);
        /** @var Checklist[] $checklists */
        $checklists = array_map(fn(ChecklistQuestion $q) => $q->getChecklist(), $tasks);
        /** @var Checklist[] $nonFinishedChecklists */
        $nonFinishedChecklists = array_filter(
            $checklists,
            fn(Checklist $c) => is_null($c->getFinishedAt()),
        );

        $result = [];

        foreach ($nonFinishedChecklists as $checklist) {
            if (in_array($checklist->getId(), $ownedChecklistsIds)) {
                continue;
            }
            $ownedChecklistsIds[] = $checklist->getId();
            /** @var ChecklistQuestion[] $questions */
            $questions = $checklist->getQuestions()->toArray();
            $checklist->getQuestions()->clear();
            foreach ($questions as $question) {
                if ($question->getResponsible() === $employee) {
                    $checklist->getQuestions()->add($question);
                }
            }
            $checklistDone = $checklist->getQuestions()->filter(function (ChecklistQuestionInterface $question) {
                if (!$question instanceof ChecklistQuestion) {
                    return false;
                }
                $currentStatus = $question->getPossibleStatuses()[$question->getCurrentStatus()];
                return !($currentStatus['done'] ?? false);
            })->isEmpty();
            if (!$checklistDone) {
                $result[] = $checklist;
            }
        }
        return $result;
    }

    /**
     * @Route("/{id}", name="delete_checklist")
     * @Method("DELETE")
     * @Security("has_role('ROLE_HR')")
     * @param int $id
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function deleteChecklist(MessageBusInterface $messageBus, int $id): View
    {
        $message = new DeleteChecklist($id);
        try {
            $messageBus->dispatch($message);
            return $this->view([], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    private function isOwner(array $owners, User $user): bool
    {
        $result = false;
        /** @var Employee $owner */
        foreach ($owners as $owner) {
            if ($owner->getId() === $user->getEmployeeId()) {
                $result = true;
                break;
            }
        }
        return $result;
    }
}
