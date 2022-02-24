<?php

namespace Divante\Bundle\AdventureBundle\Controller\Api\Statistics;

use Divante\Bundle\AdventureBundle\Infrastructure\Doctrine\Dbal\DbalStatisticCompanyQuery;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PlannerStatisticController
 *
 * @package Divante\Bundle\AdventureBundle\Controller\Api\Statistics
 * @author PK <pk@divante.com>
 * @Route("api/statistics/planner")
 */
class PlannerStatisticController extends FOSRestController
{

    /**
     * @Route("/getByYear/{year}/{tribes}", name="company_stats_by_year")
     * @Method("GET")
     * @param int $year
     * @param string $tribes
     * @param DbalStatisticCompanyQuery $query
     *
     * @return View
     */
    public function getCompanyStatsByYear(int $year, string $tribes, DbalStatisticCompanyQuery $query) :View
    {
        $tribes = json_decode($tribes);
        sort($tribes);
        try {
            $months = $query->getStatsByYearAndTribes($year, $tribes);
            return $this->view($months, Response::HTTP_OK);
        } catch (\Exception $exception) {
            return $this->view(['error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/getEmployeesByDateAndTribes/{date}/{tribes}", name="company__employees_stats_by_dates")
     * @Method("GET")
     * @param string $date
     * @param string $tribes
     *
     * @param DbalStatisticCompanyQuery $query
     *
     * @return View
     * @throws \Exception
     */
    public function getEmployeesStatsByDateAndTribes(
        string $date,
        string $tribes,
        DbalStatisticCompanyQuery $query
    ) :View {
        $tribes = json_decode($tribes);
        $date = new \DateTime($date);
        try {
            return $this->view($query->getEmployeesByDateAndTribes($date, $tribes), Response::HTTP_OK);
        } catch (\Exception $exception) {
            return $this->view(['error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/yearsofactivity", name="company_stats_yearsofactivity")
     * @Method("GET")
     * @param DbalStatisticCompanyQuery $query
     *
     * @return View
     */
    public function getYears(DbalStatisticCompanyQuery $query) :View
    {
        try {
            $years = $query->getYears();
            return $this->view($years, Response::HTTP_OK);
        } catch (\Exception $exception) {
            return $this->view(['error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/tribes", name="company_stats_tribes")
     * @Method("GET")
     * @param DbalStatisticCompanyQuery $query
     *
     * @return View
     */
    public function getTribes(DbalStatisticCompanyQuery $query) :View
    {
        try {
            $tribes = $query->getTribes();
            return $this->view($tribes, Response::HTTP_OK);
        } catch (\Exception $exception) {
            return $this->view(['error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
