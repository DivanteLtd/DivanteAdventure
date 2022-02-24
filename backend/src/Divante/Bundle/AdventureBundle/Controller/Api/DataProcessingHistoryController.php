<?php
/**
 * @package  Divante\Bundle
 * @author BH <bh@divante.pl>
 * @copyright 2017 Divante Sp. z o.o.
 * @license See LICENSE_DIVANTE.txt for license details.
 */

namespace Divante\Bundle\AdventureBundle\Controller\Api;

use Divante\Bundle\AdventureBundle\Documents\Pdf\ReportPdfDocument;
use Divante\Bundle\AdventureBundle\Entity\EmployeeProject;
use Divante\Bundle\AdventureBundle\Entity\Project;
use Divante\Bundle\AdventureBundle\Entity\Repository\EmployeeProjectRepository;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\View\View;

/**
 * Class DataProcessingHistoryController
 * @Route("api/project")
 */
class DataProcessingHistoryController extends FOSRestController
{

    /**
     * @Route("/history/{id}/download", name="data_processing_history_download")
     * @Method("GET")
     * @Security("has_role('ROLE_MANAGER')")
     * @param Request $request
     * @param Project $project
     * @param ReportPdfDocument $document
     * @return View
     * @throws \Exception
     *
     */
    public function downloadProcessingHistoryData(Request $request, Project $project, ReportPdfDocument $document)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var EmployeeProjectRepository $repo */
        $repo = $em->getRepository(EmployeeProject::class);
        $employeeProject = $repo->getAllEmployeeWorkInProject($project);
        $document->setProject($project);
        $document->setHistory($employeeProject['previously']);
        $document->setEmployeeProject($employeeProject['currently']);
        $prefix = mt_rand();
        $hash = hash('sha256', uniqid("$prefix", true));
        $this->get('translator')->setLocale($request->headers->get('adventurelanguage', 'pl'));
        $document->setFileName($hash);
        $document->buildPdf();
        return $this->view(['token' => $hash]);
    }
}
