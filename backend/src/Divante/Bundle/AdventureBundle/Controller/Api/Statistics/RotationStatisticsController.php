<?php

namespace Divante\Bundle\AdventureBundle\Controller\Api\Statistics;

use Divante\Bundle\AdventureBundle\Services\Statistics\Rotation\MonthCalculator;
use Divante\Bundle\AdventureBundle\Services\Statistics\Rotation\WholeTimeCalculator;
use Divante\Bundle\AdventureBundle\Services\Statistics\Rotation\YearCalculator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\View\View;

/**
 * Class RotationStatisticsController
 * @package Divante\Bundle\AdventureBundle\Controller\Api
 * @Route("api/statistics/rotation")
 */
class RotationStatisticsController extends FOSRestController
{
    /**
     * @Route("", name="rotation_all")
     * @Method("GET")
     * @Security("has_role('ROLE_HR')")
     * @param WholeTimeCalculator $calculator
     * @return View
     * @throws \Exception
     */
    public function getStatistics(WholeTimeCalculator $calculator) : View
    {
        return $this->view($calculator->calculate(), Response::HTTP_OK);
    }

    /**
     * @Route("/{year}", name="stats_rotation_year")
     * @Method("GET")
     * @Security("has_role('ROLE_HR')")
     * @param int $year
     * @param YearCalculator $calculator
     * @return View
     */
    public function getStatisticsForYear(int $year, YearCalculator $calculator) : View
    {
        return $this->view($calculator->calculate($year), Response::HTTP_OK);
    }

    /**
     * @Route("/{year}/{month}", name="stats_rotation_month")
     * @Method("GET")
     * @Security("has_role('ROLE_HR')")
     * @param int $year
     * @param int $month
     * @param MonthCalculator $calculator
     * @return View
     */
    public function getStatisticsForMonth(int $year, int $month, MonthCalculator $calculator) : View
    {
        if ($month < 1 || $month > 12) {
            return $this->view(['error' => 'Month should have a value from 1 to 12'], Response::HTTP_BAD_REQUEST);
        }
        return $this->view($calculator->calculate($year, $month), Response::HTTP_OK);
    }
}
