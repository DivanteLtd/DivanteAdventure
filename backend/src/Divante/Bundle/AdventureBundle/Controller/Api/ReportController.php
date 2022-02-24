<?php

namespace Divante\Bundle\AdventureBundle\Controller\Api;

use Divante\Bundle\AdventureBundle\Message\CreatePlannerReport;
use Divante\Bundle\AdventureBundle\Query\Report\ReportQuery;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Class ReportController
 * @package Divante\Bundle\AdventureBundle\Controller\Api
 * @Route("api/report")
 */
class ReportController extends FOSRestController
{
    /** @var ReportQuery */
    protected $query;
    /** @var MessageBusInterface */
    protected $messageBus;

    public function __construct(ReportQuery $query, MessageBusInterface $messageBus)
    {
        $this->query = $query;
        $this->messageBus = $messageBus;
    }


    /**
     * @Route("", name="create_report")
     * @Method("POST")
     * @param Request $request
     * @return View
     */
    public function create(Request $request) :View
    {
        $query = $request->get('query', null);
        $criteria['query'] = $query;
        $criteria['timestamp_gte'] = $request->get('timestamp_gte');
        $criteria['timestamp_lte'] = $request->get('timestamp_lte');
        $criteria['view_mode'] = $request->get('view_mode');
        try {
            $data = $this->query->getByCriteria($criteria);
            $path = $this->container->get('kernel')->getProjectDir().'/var/tmp/reports';
            $filename =  bin2hex(random_bytes(16)) . '.xlsx';
            $message = new CreatePlannerReport($data, $path . '/' . $filename);
            $this->messageBus->dispatch($message);
            return $this->view(['token' => $filename], Response::HTTP_OK);
        } catch (\Exception $exception) {
            return $this->view(['error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
