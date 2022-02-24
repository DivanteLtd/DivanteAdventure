<?php

namespace Divante\Bundle\AdventureBundle\Controller\Api;

use Collator;
use Divante\Bundle\AdventureBundle\Entity\Project;
use Divante\Bundle\AdventureBundle\Entity\Tribe;
use Divante\Bundle\AdventureBundle\Mappers\ProjectDetailMapper;
use Divante\Bundle\AdventureBundle\Message\Project\CreateProject;
use Divante\Bundle\AdventureBundle\Message\Project\DeleteProject;
use Divante\Bundle\AdventureBundle\Message\SendEmail;
use Divante\Bundle\AdventureBundle\Message\Project\UpdateProject;
use Divante\Bundle\AdventureBundle\Message\Project\HideProject;
use Divante\Bundle\AdventureBundle\Message\Project\CreateCriteriaProjectPair;
use Divante\Bundle\AdventureBundle\Message\Project\DeleteCriteriaProjectPair;
use Divante\Bundle\AdventureBundle\Query\Project\ProjectQuery;
use Divante\Bundle\AdventureBundle\Services\Slack\MessageTemplates\IntegrationDisconnectedMessage;
use Divante\Bundle\AdventureBundle\Entity\User;
use Exception;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Project controller.
 *
 * @Route("api/projects")
 */
class ProjectController extends FOSRestController
{
    /**
     * Lists all project entities.
     *
     * Access: ADMIN, USER
     *
     * @Route("", name="project_index")
     * @Method("GET")
     *
     * @Security("has_role('ROLE_USER')")
     *
     * @ApiDoc(
     *  section="Project",
     *  resource=true,
     *  description="Gets all projects",
     *  output="Divante\Bundle\AdventureBundle\Entity\Project",
     *  statusCodes={
     *         200="Returned projects list when successful",
     *         400="Bad request",
     *         403="Forbidden for this user",
     *         404="Projects not found",
     *         405="Method not allowed",
     *  }
     * )
     *
     * @param Request $request
     * @param ProjectQuery $projectQuery
     * @return View
     */
    public function indexAction(Request $request, ProjectQuery $projectQuery): View
    {
        $query = $request->get('query', '');
        $disallowStrings = ['SET', 'DROP', 'DELETE', 'UPDATE'];
        foreach ($disallowStrings as $string) {
            if (strpos(strtoupper($query), $string) !== false) {
                return $this->view(
                    ['error' => sprintf('You cannot use %s', $string)],
                    Response::HTTP_BAD_REQUEST
                );
            }
        }
        try {
            $projects = $projectQuery->getByQuery($query);
            $coll = new Collator('pl_PL');
            usort($projects, function (object $a, object $b) use ($coll) {
                return $coll->compare(strtolower($a->getName()), strtolower($b->getName()));
            });

            return $this->view($projects, Response::HTTP_OK);
        } catch (Exception $exception) {
            return $this->view($exception, Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * List all project entities with more details.
     * @Route("/details", name="project_details")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     * @param ProjectDetailMapper $mapper
     * @return View
     */
    public function listWithDetailsAction(ProjectDetailMapper $mapper) : View
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Project::class);
        /** @var Project[] $allProjects */
        $allProjects = $repo->findAll();
        $returnArray = array_map($mapper, $allProjects);
        return $this->view($returnArray, Response::HTTP_OK);
    }

    /**
     * Creates a new project entity.
     *
     * Access: MANAGER
     *
     * @Route("", name="project_new")
     * @Method("POST")
     *
     * @Security("has_role('ROLE_MANAGER')")
     *
     * @param Request $request
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function newAction(Request $request, MessageBusInterface $messageBus): View
    {
        try {
            $name = $request->get('name', '');
            $description = $request->get('description', '');
            $photo = $request->get('photo', '');
            $url = $request->get('url', '');
            $type = $request->get('project_type', 0);
            $budget = $request->get('planned_budget', 0);
            $startedAt = $request->get('started_at', null);
            $endedAt = $request->get('ended_at', null);
            $billable = $request->get('billable', null);
            $code = $request->get('code', null);
            $gitlabProjects = $request->get('gitlab_projects', []);
            $createEntry = new CreateProject(
                $name,
                $code,
                $description,
                $photo,
                $url,
                $type,
                $budget,
                $billable,
                $startedAt,
                $endedAt,
                $gitlabProjects,
            );
            $messageBus->dispatch($createEntry);

            $this->get('cache.app')->deleteItem('projects');
            return $this->view([], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Edit an existing project.
     *
     * Access: ROLE_MANAGER
     *
     * @Route("/{id}", name="project_edit")
     * @Method("PATCH")
     * @Security("has_role('ROLE_MANAGER')")
     *
     * @param int $id
     * @param Request $request
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function editAction(int $id, Request $request, MessageBusInterface $messageBus): View
    {
        $em = $this->getDoctrine()->getManager();
        try {
            /** @var Project|null $entry */
            $entry = $em->getRepository(Project::class)->find($id);
            if (is_null($entry)) {
                return $this->view(['error' => "Project with ID $id not found"], Response::HTTP_NOT_FOUND);
            }
            $name = $request->get('name', '');
            $description = $request->get('description', '');
            $photo = $request->get('photo', '');
            $url = $request->get('url', '');
            $type = $request->get('project_type', 0);
            $budget = $request->get('planned_budget', 0);
            $startedAt = $request->get('started_at', null);
            $endedAt = $request->get('ended_at', null);
            $billable = $request->get('billable', null);
            $code = $request->get('code', null);
            $gitlabProjects = $request->get('gitlab_projects', []);
            $updateEntry = new UpdateProject(
                $entry,
                $name,
                $code,
                $description,
                $photo,
                $url,
                $type,
                $budget,
                $billable,
                $startedAt,
                $endedAt,
                $gitlabProjects,
            );
            $messageBus->dispatch($updateEntry);
            $this->get('cache.app')->deleteItem('projects');
            return $this->view([], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Hide an existing project.
     *
     * Access: ROLE_MANAGER
     *
     * @Route("/hide/{id}", name="project_hide")
     * @Method("PATCH")
     * @Security("has_role('ROLE_MANAGER')")
     *
     * @param int $id
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function hideAction(int $id, MessageBusInterface $messageBus): View
    {
        $em = $this->getDoctrine()->getManager();
        try {
            /** @var Project|null $entry */
            $entry = $em->getRepository(Project::class)->find($id);
            if (is_null($entry)) {
                return $this->view(['error' => "Project with ID $id not found"], Response::HTTP_NOT_FOUND);
            }
            $hideEntry = new HideProject($entry, true);
            $messageBus->dispatch($hideEntry);
            $this->get('cache.app')->deleteItem('projects');
            return $this->view([], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Delete tribe
     *
     * Access: ROLE_MANAGER
     *
     * @Route("/{id}", name="project_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_MANAGER')")
     *
     * @param int $id
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function deleteAction(int $id, MessageBusInterface $messageBus): View
    {
        $message = new DeleteProject($id);
        try {
            $messageBus->dispatch($message);
            $this->get('cache.app')->deleteItem('projects');
            return $this->view([], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/{projectId}/criterium")
     * @Method("POST")
     * @Security("has_role('ROLE_MANAGER')")
     * @param Request $request
     * @param int $projectId
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function addCriterion(Request $request, int $projectId, MessageBusInterface $messageBus) : View
    {
        try {
            /** @var int|null $criterionId */
            $criterionId = $request->get('criterionId');
            if (is_null($criterionId)) {
                throw new BadRequestHttpException("Lacking parameter criterionId");
            }
            $message = new CreateCriteriaProjectPair($projectId, $criterionId, $this->getDoctrine()->getManager());
            $messageBus->dispatch($message);
            return $this->view([], Response::HTTP_OK);
        } catch (NotFoundHttpException $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (BadRequestHttpException $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/{projectId}/criterium/{criteriumId}")
     * @Method("DELETE")
     * @Security("has_role('ROLE_MANAGER')")
     * @param int $projectId
     * @param int $criteriumId
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function removeCriteria(int $projectId, int $criteriumId, MessageBusInterface $messageBus) : View
    {
        try {
            $message = new DeleteCriteriaProjectPair($projectId, $criteriumId, $this->getDoctrine()->getManager());
            $messageBus->dispatch($message);
            return $this->view([], Response::HTTP_OK);
        } catch (NotFoundHttpException $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (BadRequestHttpException $e) {
            return $this->view(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/sendEmail/{projectId}")
     * @Method("GET")
     * @Security("has_role('ROLE_MANAGER')")
     * @param int $projectId
     * @param MessageBusInterface $messageBus
     * @return View
     */
    public function sendEmailWithCriteria(int $projectId, MessageBusInterface $messageBus) : View
    {
        try {
            $message = new SendEmail($projectId);
            $messageBus->dispatch($message);
            return $this->view([], Response::HTTP_OK);
        } catch (Exception $ex) {
            return $this->view(['error' => $ex->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/{projectId}/disconnectSlack", name="project_disconnect_slack")
     * @Method("POST")
     * @Security("has_role('ROLE_MANAGER')")
     * @param int $projectId
     * @param IntegrationDisconnectedMessage $template
     * @return View
     * @throws Exception
     */
    public function disconnectFromSlack(int $projectId, IntegrationDisconnectedMessage $template) : View
    {
        /** @var Project|null $project */
        $project = $this->getDoctrine()->getRepository(Project::class)->find($projectId);
        /** @var User $user */
        $user = $this->getUser();
        if (!is_null($project)) {
            $template
                ->setReceiver($project)
                ->setResponsible($user->getEmployee())
                ->send();
            $project->setSlackStatus(Tribe::SLACK_STATUS_NOT_ASKED);
            $this->getDoctrine()->getManager()->flush();
            return $this->view([], Response::HTTP_OK);
        } else {
            return $this->view([], Response::HTTP_NOT_FOUND);
        }
    }
}
