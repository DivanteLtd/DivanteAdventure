<?php

namespace Divante\Bundle\AdventureBundle\EventSubscriber\JWT;

use DateTime;
use Divante\Bundle\AdventureBundle\Entity\Employee;
use Divante\Bundle\AdventureBundle\Entity\PotentialEmployee;
use Divante\Bundle\AdventureBundle\Entity\Repository\PotentialEmployeeRepository;
use Divante\Bundle\AdventureBundle\Entity\User;
use Divante\Bundle\AdventureBundle\Infrastructure\Config\EnvironmentSettings;
use Divante\Bundle\AdventureBundle\Representation\Email\EmailConfiguration;
use Divante\Bundle\AdventureBundle\Security\Core\User\OAuthUserProvider;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\TranslatorInterface;

class JWTCreatedSubscriber implements EventSubscriberInterface
{
    private RequestStack $requestStack;
    private EntityManagerInterface $em;
    private OAuthUserProvider $pathUserResponse;
    private EmailConfiguration $email;
    protected TranslatorInterface $translator;
    private EnvironmentSettings $envSettings;

    public function __construct(
        RequestStack $requestStack,
        EntityManagerInterface $em,
        OAuthUserProvider $pathUserResponse,
        EmailConfiguration $email,
        TranslatorInterface $translator,
        EnvironmentSettings $envSettings
    ) {
        $this->requestStack = $requestStack;
        $this->em = $em;
        $this->pathUserResponse = $pathUserResponse;
        $this->email = $email;
        $this->translator = $translator;
        $this->envSettings = $envSettings;
    }

    /** @return array<string,array<int,array<int,string|int>>> */
    public static function getSubscribedEvents() : array
    {
        return [
            Events::JWT_CREATED => [
                [ 'onJwtCreated', 0 ],
            ],
        ];
    }

    public function onJwtCreated(JWTCreatedEvent $event): void
    {
        if (!($request = $this->requestStack->getCurrentRequest())) {
            return;
        }

        $user = $event->getUser();
        if (!$user instanceof User) {
            return;
        }

        $payload = $event->getData();

        if (empty($user->getEmployeeId())) {
            $this->createEmployee($user);
        }

        $payload['ip'] = $request->getClientIp();
        $payload['employeeId'] = $user->getEmployeeId();
        $payload['roles'] = $user->getRoles();
        if ($user->getEmployee()->getHashedPin() !== null) {
            $payload['pin'] = $user->getEmployee()->getHashedPin();
        }
        $seconds = $user->getEmployee()->getTokenExpirationSeconds();
        $expiration = new \DateTime('now');
        $expiration->add(new \DateInterval(sprintf('PT%sS', $seconds)));
        $payload['exp'] = $expiration->getTimestamp();
        $event->setData($payload);
    }


    private function createEmployee(User $user): Employee
    {
        $repo = $this->em->getRepository(Employee::class);
        /** @var Employee|null $employee */
        $employee = $repo->findOneBy(['email' => $user->getEmail()]);

        if (!empty($employee)) {
            $this->updateUser($user, $employee);
            $edit = false;
            if (!$employee->getPhoto()) {
                $employee->setPhoto($this->pathUserResponse->socialResponse->getProfilePicture());
                $edit = true;
            }
            if (!$employee->getName()) {
                $employee->setName($this->pathUserResponse->socialResponse->getFirstName());
                $edit = true;
            }
            if (!$employee->getLastName()) {
                $employee->setLastName($this->pathUserResponse->socialResponse->getLastName());
                $edit = true;
            }
            if ($edit == true) {
                $employee->setUpdatedAt();
                $this->em->persist($employee);
                $this->em->flush();
            }

            return $employee;
        }

        $employee = new Employee();
        $employee->setEmail($user->getEmail());
        $employee->setName($this->pathUserResponse->socialResponse->getFirstName());
        $employee->setLastName($this->pathUserResponse->socialResponse->getLastName());
        $employee->setPhoto($this->pathUserResponse->socialResponse->getProfilePicture());
        $employee->setCreatedAt();
        $employee->setUpdatedAt();
        $employee->setDateResetPin(new DateTime());
        $employee->setEmployeeCode($user->getId());

        $this->em->persist($employee);
        $this->em->flush();
        $this->updateUser($user, $employee);
        $this->copyData($employee);
        $language = $employee->getLanguage();
        $this->translator->setLocale($language);
        $subject = $this->translator->trans('welcomeToAdventure');
        $this->email->sendMessage(
            $employee->getEmail(),
            null,
            sprintf('%s, %s', $employee->getName(), $subject),
            ['employee' => $employee],
            'AdventureBundle:Emails:register.html.twig'
        );
        $this->changeStatus($user->getEmail());
        return $employee;
    }

    private function updateUser(User $user, Employee $employee): bool
    {
        $user->setEmployee($employee);
        if ($this->envSettings->isDemoEnabled()) {
            $user->setRoles([ 'ROLE_SUPER_ADMIN' ]);
        }
        $this->em->persist($user);
        $this->em->flush();

        return true;
    }

    private function changeStatus(string  $email) :void
    {
        /** @var PotentialEmployeeRepository $repo */
        $repo = $this->em->getRepository(PotentialEmployee::class);
        /** @var PotentialEmployee|null $entity */
        $entity = $repo->findOneByEmailAddressUsername($email);
        if ($entity) {
            $entity->setStatus(PotentialEmployee::STATUS_ACCEPTED);
            $entity->setWork(true);
            $this->em->persist($entity);
            $this->em->flush();
        }
    }

    private function copyData(Employee $employee): void
    {
        /** @var PotentialEmployeeRepository $repo */
        $repo = $this->em->getRepository(PotentialEmployee::class);
        /** @var PotentialEmployee|null $entity */
        $entity = $repo->findOneByEmailAddressUsername($employee->getEmail());
        if ($entity && $entity->getStatus() === PotentialEmployee::STATUS_ACCEPTED) {
            $employee->setName($entity->getName());
            $employee->setLastName($entity->getLastName());
            $employee->setCity($entity->getCity());
            $employee->setPostalCode($entity->getPostalCode());
            $employee->setStreet($entity->getStreet());
            $employee->setCountry($entity->getCountry());
            $employee->setDateOfBirth($entity->getDateOfBirth());
            $employee->setPrivatePhone($entity->getPrivatePhone());
            $employee->setGender($entity->getGender());
            if (!is_null($entity->getDesignatedTribe())) {
                $employee->setTribe($entity->getDesignatedTribe());
            };
            if (!is_null($entity->getDesignatedPosition())) {
                $employee->setPosition($entity->getDesignatedPosition());
            }
            if (is_null($entity->getDesignatedHireDate())) {
                $employee->setHiredAt(new DateTime());
            } else {
                $employee->setHiredAt($entity->getDesignatedHireDate());
            }
            $employee->setContractId(Employee::getContractIdByName($entity->getContractType()));
            $employee->setNip($entity->getNip());
            $employee->setFirmName($entity->getFirmName());
            $employee->setFirmAddress($entity->getFirmAddress());
            $this->em->persist($employee);
            $this->em->flush();
        }
    }
}
