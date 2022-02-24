<?php

namespace Divante\Bundle\AdventureBundle\Controller\Api\Statistics;

use Divante\Bundle\AdventureBundle\Services\Statistics\Rotation\ActualCalculator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\View\View;

/**
 * Class HrStatisticsController
 * @package Divante\Bundle\AdventureBundle\Controller\Api
 * @Route("api/statistics")
 */
class HrStatisticsController extends FOSRestController
{
    /**
     * @Route("/actual", name="hr_statistics_actual")
     * @Security("has_role('ROLE_HR')")
     * @Method("GET")
     * @param ActualCalculator $calculator
     * @return View
     */
    public function getActualStatistics(ActualCalculator $calculator) : View
    {
        return $this->view($calculator->calculate(), Response::HTTP_OK);
    }
}
