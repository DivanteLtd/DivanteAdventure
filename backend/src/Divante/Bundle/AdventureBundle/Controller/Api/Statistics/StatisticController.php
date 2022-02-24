<?php

namespace Divante\Bundle\AdventureBundle\Controller\Api\Statistics;

use Divante\Bundle\AdventureBundle\Query\Statistic\EmployeeQuery;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\View\View;

/**
 * Class StatisticController
 * @package Divante\Bundle\AdventureBundle\Controller\Api
 * @Route("api/statistic")
 */
class StatisticController extends FOSRestController
{
    /** @var EmployeeQuery */
    private $employeeQuery;

    /**
     * StatisticController constructor.
     * @param EmployeeQuery $employeeQuery
     */
    public function __construct(EmployeeQuery $employeeQuery)
    {
        $this->employeeQuery = $employeeQuery;
    }

    /**
     * @Route("/rotate", name="rotate")
     * @Security("has_role('ROLE_HR')")
     * @Method("GET")
     */
    public function rotateStatisticAction() : View
    {
        return $this->view($this->employeeQuery->getAll(), Response::HTTP_OK);
    }

    /**
     * @Route("/rotate/general", name="rotateGeneral")
     * @Security("has_role('ROLE_HR')")
     * @Method("GET")
     */
    public function rotateGeneralStatisticAction() : View
    {
        return $this->view($this->employeeQuery->getAllGeneral(), Response::HTTP_OK);
    }
}
