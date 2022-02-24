<?php

namespace Divante\Bundle\AdventureBundle\Controller;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\Project;
use Divante\Bundle\AdventureBundle\Entity\Tribe;
use Divante\Bundle\AdventureBundle\Infrastructure\Config\SystemConfig;
use Divante\Bundle\AdventureBundle\Infrastructure\Config\FrontendUrlSupplier;
use Divante\Bundle\AdventureBundle\Services\Slack\Api\SlackReceiver;
use Divante\Bundle\AdventureBundle\Services\Slack\Api\SlackSender;
use Divante\Bundle\AdventureBundle\Services\Slack\MessageTemplates\WelcomeAfterIntegrationMessage;
use Divante\Bundle\AdventureBundle\Services\Slack\RandomStateHandler;
use Divante\Bundle\AdventureBundle\Services\Slack\SlackAdminReceiver;
use FOS\RestBundle\Controller\FOSRestController;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Psr\Http\Message\StreamInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SlackController
 * @package Divante\Bundle\AdventureBundle\Controller
 * @Route("slack")
 */
class SlackController extends FOSRestController
{
    private string $clientId;
    private string $clientSecret;
    private string $redirectUri;
    private string $frontendUrl;
    private SlackAdminReceiver $adminReceiver;

    public function __construct(SystemConfig $config, FrontendUrlSupplier $url, SlackAdminReceiver $adminReceiver)
    {
        $this->clientId = $config->getValueOrDefault(SystemConfig::KEY_SLACK_CLIENT_ID, '');
        $this->clientSecret = $config->getValueOrDefault(SystemConfig::KEY_SLACK_CLIENT_SECRET, '');
        $this->redirectUri = $config->getValueOrDefault(SystemConfig::KEY_SLACK_REDIRECT_URI, '');
        $this->frontendUrl = $url->getFrontendUrl();
        $this->adminReceiver = $adminReceiver;
    }

    /**
     * @Route("/scopes", name="slack_scopes")
     * @Method("GET")
     * @param Request $request
     * @param RandomStateHandler $stateHandler
     * @param WelcomeAfterIntegrationMessage $integrationMessage
     * @param SlackSender $sender
     * @return Response
     * @throws \Exception
     */
    public function scopesAction(
        Request $request,
        RandomStateHandler $stateHandler,
        WelcomeAfterIntegrationMessage $integrationMessage,
        SlackSender $sender
    ) : Response {
        $code = $request->get('code');
        $state = $request->get('state');
        $receiver = $stateHandler->getReceiverByState($state);
        if (is_null($receiver)) {
            return $this->redirect($this->frontendUrl);
        }
        $client = new Client();
        try {
            $result = $client->request('POST', "https://slack.com/api/oauth.access", [
                'form_params' => [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'code' => $code,
                    'redirect_uri' => $this->redirectUri
                ]
            ]);
        } catch (GuzzleException $e) {
            return $this->redirect($this->frontendUrl);
        }
        $body = $this->getBody($result->getBody());
        $array = json_decode($body, true);
        $accessToken = $array['access_token'];
        $userId = $receiver instanceof Employee ? $array['user_id'] : $array['incoming_webhook']['channel_id'];
        $receiver
            ->setSlackAccessToken($accessToken)
            ->setSlackId($userId)
            ->setSlackStatus(Employee::SLACK_AUTHORIZED);
        $this->getDoctrine()->getManager()->flush();

        $sender->send(
            $integrationMessage->setReceiver($receiver)->getMessage(),
            $receiver
        );
        return $this->redirect($this->frontendUrl);
    }


    /**
     * @Route("/redirectUser", name="slack_redirect_user")
     * @Method("GET")
     * @param Request $request
     * @param JWTEncoderInterface $encoder
     * @param RandomStateHandler $stateHandler
     * @return Response
     */
    public function slackRedirection(
        Request $request,
        JWTEncoderInterface $encoder,
        RandomStateHandler $stateHandler
    ) : Response {

        $receiver = $this->getReceiver($request, $encoder);
        if (is_null($receiver)) {
            return $this->redirect($this->frontendUrl);
        }
        $scope = $receiver instanceof Employee ? 'chat:write:bot' : 'incoming-webhook';
        $state = $stateHandler->generateRandomState($receiver);
        $route = sprintf(
            "https://slack.com/oauth/authorize?scope=%s&client_id=%s&redirect_uri=%s&state=%s",
            $scope,
            $this->clientId,
            $this->redirectUri,
            $state
        );
        $this->getDoctrine()->getManager()->flush();
        return $this->redirect($route, Response::HTTP_FOUND);
    }

    private function getReceiver(Request $request, JWTEncoderInterface $encoder) : ?SlackReceiver
    {
        $employee = $this->getEmployee($request->get('token'), $encoder);
        $type = $request->get('type', 'employee');
        $id = $request->get('id', null);

        if ($type === 'employee') {
            return $employee;
        } elseif ($type === 'tribe' && $employee->isTribeMaster()) {
            /** @var Tribe|null $tribe */
            $tribe = $this->getDoctrine()->getRepository(Tribe::class)->find($id);
            return $tribe;
        } elseif ($type === 'project' && $employee->isManager()) {
            /** @var Project|null $project */
            $project = $this->getDoctrine()->getRepository(Project::class)->find($id);
            return $project;
        } elseif ($type === 'admin' && $employee->isSuperAdmin()) {
            return $this->adminReceiver;
        } else {
            return null;
        }
    }

    private function getEmployee(string $token, JWTEncoderInterface $encoder) : ?Employee
    {
        try {
            $decoded = $encoder->decode($token);
            /** @var int $employeeId */
            $employeeId = (int)$decoded['employeeId'];
            $repo = $this->getDoctrine()->getRepository(Employee::class);
            /** @var Employee|null $employee */
            $employee = $repo->find($employeeId);
            return $employee;
        } catch (JWTDecodeFailureException $e) {
            return null;
        }
    }

    private function getBody(StreamInterface $body) : string
    {
        $result = "";
        do {
            $part = $body->read(1024);
            $result .= $part;
        } while (strlen($part) > 0);
        return $result;
    }
}
