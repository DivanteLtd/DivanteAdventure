<?php

namespace Divante\Bundle\AdventureBundle\Controller\Api;

use Divante\Bundle\AdventureBundle\Entity\GitlabProject;
use Divante\Bundle\AdventureBundle\Mappers\GitlabProjectMapper;
use \FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\Controller\FOSRestController;

/**
 * Class IntegrationsController
 * @package Divante\Bundle\AdventureBundle\Controller\Api
 * @Route("api/integrations")
 */
class IntegrationsController extends FOSRestController
{
    /**
     * @Route("/gitlab", name="integrations_gitlab_projects")
     * @Method("GET")
     * @Security("has_role('ROLE_MANAGER')")
     * @param GitlabProjectMapper $mapper
     * @return View
     */
    public function getGitlabProjectsList(GitlabProjectMapper $mapper) : View
    {
        $em = $this->getDoctrine();
        $repo = $em->getRepository(GitlabProject::class);
        $projects = $repo->findAll();
        $response = array_map($mapper, $projects);
        return $this->view($response);
    }
}
