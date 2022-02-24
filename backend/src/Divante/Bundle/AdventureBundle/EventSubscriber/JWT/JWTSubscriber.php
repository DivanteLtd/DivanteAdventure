<?php

namespace Divante\Bundle\AdventureBundle\EventSubscriber\JWT;

use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Infrastructure\Config\AllowedDomains;
use Divante\Bundle\AdventureBundle\Infrastructure\Config\FrontendUrlSupplier;
use Divante\Bundle\AdventureBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTDecodedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use function Clue\StreamFilter\remove;

class JWTSubscriber implements EventSubscriberInterface
{

    private RequestStack $requestStack;
    private string $frontendAppUrl;
    private EntityManagerInterface $em;
    private array $allowedDomains;

    public function __construct(
        RequestStack $requestStack,
        FrontendUrlSupplier $urlSupplier,
        EntityManagerInterface $em,
        AllowedDomains $allowedDomains
    ) {
        $this->requestStack = $requestStack;
        $this->frontendAppUrl = $urlSupplier->getFrontendUrl();
        $this->allowedDomains = $allowedDomains->getDomains();
        $this->em = $em;
    }

    /** @return array<string,array<int,array<int,string|int>>> */
    public static function getSubscribedEvents() : array
    {
        return [
            Events::AUTHENTICATION_SUCCESS => [
                [ 'onAuthenticationSuccessResponse', 0 ],
            ],
            Events::JWT_DECODED => [
                [ 'onJwtDecoded', 0 ],
            ],
        ];
    }

    public function onJwtDecoded(JWTDecodedEvent $event) : void
    {
        if (!($request = $this->requestStack->getCurrentRequest())) {
            return;
        }
        $payload = $event->getPayload();
        if (!isset($payload['ip']) || $payload['ip'] !== $request->getClientIp()) {
            $event->markAsInvalid();
        }
        $employee = $this->getEmployeeById($payload['employeeId']);
        if ($employee->getHashedPin() !== null) {
            if (!isset($payload['pin']) || $payload['pin'] !== $employee->getHashedPin()) {
                $event->markAsInvalid();
            }
            if ($employee->getContractId() === Employee::CONTRACT_OUTSOURCE) {
                $event->markAsInvalid();
            }
            $allow = false;
            foreach ($this->allowedDomains as $allowedDomain) {
                if (strpos($employee->getEmail(), $allowedDomain) !== false) {
                    $allow = true;
                }
            }
            if ($allow === false) {
                $event->markAsInvalid();
                $employee->setContractId(Employee::CONTRACT_OUTSOURCE);
                $this->em->persist($employee);
                $this->em->flush();
            }
        }
    }

    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event) : void
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof User) {
            return;
        }

        $data['data'] = [
            'employeeId' => $user->getEmployeeId(),
            'username' => $user->getUsername(),
            'roles' => $user->getRoles()
        ];

        $event->setData($data);

        if ($this->requestStack->getCurrentRequest()->getMethod() !== "POST") {
            $event->getResponse()->headers->set(
                "Location",
                sprintf(
                    "%s#/login?token=%s&refresh_token=%s",
                    $this->frontendAppUrl,
                    $data['token'],
                    $data['refresh_token']
                )
            );
        }
    }

    private function getEmployeeById(int $id) :Employee
    {
        $employee = $this->em->getRepository(Employee::class)->find($id);
        if (is_null($employee)) {
            throw new NotFoundHttpException("Employee with id $id not found");
        }
        return $employee;
    }
}
